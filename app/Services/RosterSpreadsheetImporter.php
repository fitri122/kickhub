<?php

namespace App\Services;

use App\Models\Jurulatih;
use App\Models\Pasukan;
use App\Models\Pemain;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use OpenSpout\Common\Exception\IOException;
use OpenSpout\Reader\CSV\Reader as CSVReader;
use OpenSpout\Reader\Exception\ReaderException;
use OpenSpout\Reader\ReaderInterface;
use OpenSpout\Reader\XLSX\Reader as XLSXReader;

class RosterSpreadsheetImporter
{
    private const MAX_ROWS = 500;

    /**
     * @return int Number of imported players
     */
    public function importPlayers(Pasukan $team, UploadedFile $file): int
    {
        $rows = $this->readRows($file, ['name', 'national_id', 'jersey_number', 'age', 'role', 'position'], 'players_file');
        $validatedRows = [];
        $seenNationalIds = [];

        foreach ($rows as $row) {
            $validator = Validator::make($row['data'], [
                'name' => ['required', 'string', 'max:255'],
                'national_id' => ['required', 'digits:12'],
                'jersey_number' => ['required', 'integer', 'min:0', 'max:65535'],
                'age' => ['nullable', 'integer', 'min:1', 'max:255'],
                'role' => ['required', 'string', 'max:255'],
                'position' => ['required', 'string', 'max:255'],
            ]);

            if ($validator->fails()) {
                $this->failForRow($row['number'], $validator->errors()->first(), 'players_file');
            }

            $nationalId = $row['data']['national_id'];

            if (isset($seenNationalIds[$nationalId])) {
                $this->failForRow($row['number'], 'The national ID is duplicated in this file.', 'players_file');
            }

            $seenNationalIds[$nationalId] = true;
            $validatedRows[] = $validator->validated();
        }

        $existingNationalIds = Pemain::query()
            ->whereIn('no_ic', array_keys($seenNationalIds))
            ->pluck('no_ic')
            ->all();

        if ($existingNationalIds !== []) {
            $this->failForRow(0, 'National ID '.$existingNationalIds[0].' is already registered.', 'players_file');
        }

        DB::transaction(function () use ($team, $validatedRows): void {
            foreach ($validatedRows as $row) {
                $team->pemain()->create([
                    'nama' => $row['name'],
                    'no_ic' => $row['national_id'],
                    'no_jersi' => $row['jersey_number'],
                    'umur' => $row['age'] ?: null,
                    'role_pemain' => $row['role'],
                    'position' => $row['position'],
                ]);
            }
        });

        return count($validatedRows);
    }

    /**
     * @return int Number of imported coaches
     */
    public function importCoaches(Pasukan $team, UploadedFile $file): int
    {
        $rows = $this->readRows($file, ['name', 'age'], 'coaches_file');
        $validatedRows = [];
        $seenNames = [];

        foreach ($rows as $row) {
            $validator = Validator::make($row['data'], [
                'name' => ['required', 'string', 'max:255'],
                'age' => ['required', 'integer', 'min:1', 'max:255'],
            ]);

            if ($validator->fails()) {
                $this->failForRow($row['number'], $validator->errors()->first(), 'coaches_file');
            }

            $name = $row['data']['name'];

            if (isset($seenNames[mb_strtolower($name)])) {
                $this->failForRow($row['number'], 'The coach name is duplicated in this file.', 'coaches_file');
            }

            $seenNames[mb_strtolower($name)] = true;
            $validatedRows[] = $validator->validated();
        }

        $existingName = Jurulatih::query()
            ->where('id_pasukan', $team->id)
            ->whereIn('nama', array_column($validatedRows, 'name'))
            ->value('nama');

        if ($existingName !== null) {
            $this->failForRow(0, 'Coach '.$existingName.' is already registered for this team.', 'coaches_file');
        }

        DB::transaction(function () use ($team, $validatedRows): void {
            foreach ($validatedRows as $row) {
                $team->jurulatih()->create([
                    'nama' => $row['name'],
                    'umur' => $row['age'],
                ]);
            }
        });

        return count($validatedRows);
    }

    /**
     * @param  list<string>  $requiredHeaders
     * @return list<array{number: int, data: array<string, string|null>}>
     */
    private function readRows(UploadedFile $file, array $requiredHeaders, string $errorKey): array
    {
        $reader = match (strtolower($file->getClientOriginalExtension())) {
            'csv' => new CSVReader,
            'xlsx' => new XLSXReader,
            default => null,
        };

        if (! $reader instanceof ReaderInterface) {
            throw ValidationException::withMessages([
                $errorKey => __('Upload a CSV or XLSX file.'),
            ]);
        }

        $rows = [];

        try {
            $reader->open($file->getRealPath());
            $sheet = null;

            foreach ($reader->getSheetIterator() as $availableSheet) {
                $sheet = $availableSheet;

                break;
            }

            if ($sheet === null) {
                throw ValidationException::withMessages([
                    $errorKey => __('The spreadsheet does not contain a worksheet.'),
                ]);
            }

            $headers = null;
            $rowNumber = 0;

            foreach ($sheet->getRowIterator() as $spreadsheetRow) {
                $rowNumber++;
                $values = array_map(fn (mixed $value): string => $this->cellValue($value), $spreadsheetRow->toArray());

                if ($headers === null) {
                    $headers = array_map(
                        fn (string $header): string => strtolower(str_replace([' ', '-'], '_', trim($header, "\xEF\xBB\xBF \t\n\r\0\x0B"))),
                        $values,
                    );

                    $nonEmptyHeaders = array_values(array_filter($headers));

                    if (count($nonEmptyHeaders) !== count(array_unique($nonEmptyHeaders))) {
                        $this->failForRow(1, 'Column headers must not be duplicated.', $errorKey);
                    }

                    $missingHeaders = array_diff($requiredHeaders, $headers);

                    if ($missingHeaders !== []) {
                        throw ValidationException::withMessages([
                            $errorKey => __('Missing required columns: :columns.', [
                                'columns' => implode(', ', $missingHeaders),
                            ]),
                        ]);
                    }

                    continue;
                }

                if (count(array_filter($values, fn (string $value): bool => $value !== '')) === 0) {
                    continue;
                }

                if (count($rows) >= self::MAX_ROWS) {
                    throw ValidationException::withMessages([
                        $errorKey => __('A spreadsheet can contain no more than :count data rows.', [
                            'count' => self::MAX_ROWS,
                        ]),
                    ]);
                }

                $data = [];

                foreach ($requiredHeaders as $header) {
                    $index = array_search($header, $headers, true);
                    $value = $index === false ? '' : ($values[$index] ?? '');
                    $data[$header] = $value === '' ? null : $value;
                }

                $rows[] = ['number' => $rowNumber, 'data' => $data];
            }

            if ($headers === null) {
                throw ValidationException::withMessages([
                    $errorKey => __('The spreadsheet is empty.'),
                ]);
            }

            if ($rows === []) {
                throw ValidationException::withMessages([
                    $errorKey => __('The spreadsheet does not contain any data rows.'),
                ]);
            }
        } catch (IOException|ReaderException $exception) {
            throw ValidationException::withMessages([
                $errorKey => __('The spreadsheet could not be read. Check that the file is a valid CSV or XLSX workbook.'),
            ]);
        } finally {
            $reader->close();
        }

        return $rows;
    }

    private function cellValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_float($value) && floor($value) === $value) {
            return (string) (int) $value;
        }

        return trim((string) $value);
    }

    private function failForRow(int $rowNumber, string $message, string $errorKey): never
    {
        $prefix = $rowNumber === 0 ? '' : __('Row :row: ', ['row' => $rowNumber]);

        throw ValidationException::withMessages([
            $errorKey => $prefix.$message,
        ]);
    }
}
