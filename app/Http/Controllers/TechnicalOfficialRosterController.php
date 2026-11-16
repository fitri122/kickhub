<?php

namespace App\Http\Controllers;

use App\Models\Pasukan;
use App\Models\Pemain;
use App\Services\RosterSpreadsheetImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TechnicalOfficialRosterController extends Controller
{
    public function __construct(private RosterSpreadsheetImporter $importer) {}

    public function index(): View
    {
        return view('technical-official.rosters.index', [
            'teams' => Pasukan::query()
                ->withCount(['pemain', 'jurulatih'])
                ->orderBy('nama_pasukan')
                ->get(),
        ]);
    }

    public function show(Pasukan $pasukan): View
    {
        return view('technical-official.rosters.show', [
            'team' => $pasukan,
            'players' => $pasukan->pemain()
                ->with('pencapaian')
                ->orderBy('nama')
                ->paginate(30),
            'coaches' => $pasukan->jurulatih()->orderBy('nama')->paginate(30),
        ]);
    }

    public function editPlayer(Pasukan $pasukan, Pemain $pemain): View
    {
        $this->ensurePlayerBelongsToTeam($pasukan, $pemain);
        $pemain->load('pencapaian');

        return view('technical-official.rosters.edit-player', [
            'team' => $pasukan,
            'player' => $pemain,
            'achievementRows' => $pemain->pencapaian
                ->map(fn ($achievement): array => [
                    'name' => $achievement->nama_pencapaian,
                    'year' => $achievement->tahun,
                    'level' => $achievement->peringkat,
                ])
                ->concat(array_fill(0, 3, ['name' => '', 'year' => '', 'level' => '']))
                ->values(),
        ]);
    }

    public function storePlayer(Request $request, Pasukan $pasukan): RedirectResponse
    {
        $validated = $request->validate([
            'player_name' => ['required', 'string', 'max:255'],
            'player_national_id' => ['required', 'digits:12', Rule::unique('pemain', 'no_ic')],
            'player_jersey_number' => ['required', 'integer', 'min:1', 'max:65535'],
            'player_age' => ['nullable', 'integer', 'min:1', 'max:255'],
            'player_role' => ['required', 'string', 'max:255'],
            'player_position' => ['required', 'string', 'max:255'],
        ]);

        $pasukan->pemain()->create([
            'nama' => $validated['player_name'],
            'no_ic' => $validated['player_national_id'],
            'no_jersi' => $validated['player_jersey_number'],
            'umur' => $validated['player_age'] ?? null,
            'role_pemain' => $validated['player_role'],
            'position' => $validated['player_position'],
        ]);

        return redirect()
            ->route('technical-official.rosters.teams.show', $pasukan)
            ->with('status', __('Player added successfully.'));
    }

    public function updatePlayer(Request $request, Pasukan $pasukan, Pemain $pemain): RedirectResponse
    {
        $this->ensurePlayerBelongsToTeam($pasukan, $pemain);
        $validated = $this->validatePlayer($request, $pemain);

        DB::transaction(function () use ($pemain, $validated): void {
            $pemain->update($validated['player']);
            $pemain->pencapaian()->delete();
            $this->saveAchievements($pemain, $validated['achievements']);
        });

        return redirect()
            ->route('technical-official.rosters.players.edit', [$pasukan, $pemain])
            ->with('status', __('Player details updated successfully.'));
    }

    public function storeCoach(Request $request, Pasukan $pasukan): RedirectResponse
    {
        $validated = $request->validate([
            'coach_name' => ['required', 'string', 'max:255', Rule::unique('jurulatih', 'nama')->where('id_pasukan', $pasukan->id)],
            'coach_age' => ['required', 'integer', 'min:1', 'max:255'],
        ]);

        $pasukan->jurulatih()->create([
            'nama' => $validated['coach_name'],
            'umur' => $validated['coach_age'],
        ]);

        return redirect()
            ->route('technical-official.rosters.teams.show', $pasukan)
            ->with('status', __('Coach added successfully.'));
    }

    public function importPlayers(Request $request, Pasukan $pasukan): RedirectResponse
    {
        $validated = $request->validate([
            'players_file' => ['required', 'file', 'mimes:csv,txt,xlsx', 'extensions:csv,xlsx', 'max:5120'],
        ]);
        $count = $this->importer->importPlayers($pasukan, $validated['players_file']);

        return redirect()
            ->route('technical-official.rosters.teams.show', $pasukan)
            ->with('status', __(':count players imported successfully.', ['count' => $count]));
    }

    public function importCoaches(Request $request, Pasukan $pasukan): RedirectResponse
    {
        $validated = $request->validate([
            'coaches_file' => ['required', 'file', 'mimes:csv,txt,xlsx', 'extensions:csv,xlsx', 'max:5120'],
        ]);
        $count = $this->importer->importCoaches($pasukan, $validated['coaches_file']);

        return redirect()
            ->route('technical-official.rosters.teams.show', $pasukan)
            ->with('status', __(':count coaches imported successfully.', ['count' => $count]));
    }

    public function playerTemplate(): StreamedResponse
    {
        return $this->downloadTemplate(
            'player-roster-template.csv',
            "name,national_id,jersey_number,age,role,position\n",
        );
    }

    public function coachTemplate(): StreamedResponse
    {
        return $this->downloadTemplate(
            'coach-roster-template.csv',
            "name,age\n",
        );
    }

    /**
     * @return array{player: array{nama: string, no_ic: string, no_jersi: int, umur: ?int, role_pemain: string, position: string}, achievements: list<array{nama_pencapaian: string, tahun: int, peringkat: string}>}
     */
    private function validatePlayer(Request $request, ?Pemain $pemain = null): array
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'national_id' => [
                'required',
                'digits:12',
                Rule::unique('pemain', 'no_ic')->ignore($pemain?->id),
            ],
            'jersey_number' => ['required', 'integer', 'min:1', 'max:65535'],
            'age' => ['nullable', 'integer', 'min:1', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'achievements' => ['nullable', 'array', 'max:40'],
            'achievements.*.name' => ['nullable', 'string', 'max:255'],
            'achievements.*.year' => ['nullable', 'integer', 'min:1901', 'max:2155'],
            'achievements.*.level' => ['nullable', 'string', 'max:255'],
        ]);

        $validator->after(function ($validator) use ($request): void {
            foreach ($request->input('achievements', []) as $index => $achievement) {
                $values = array_filter(
                    $achievement,
                    fn (mixed $value): bool => $value !== null && trim((string) $value) !== '',
                );

                if ($values !== [] && count($values) !== 3) {
                    $validator->errors()->add(
                        "achievements.{$index}.name",
                        __('Complete all fields for each achievement.'),
                    );
                }
            }
        });

        $validated = $validator->validate();
        $achievements = collect($validated['achievements'] ?? [])
            ->filter(fn (array $achievement): bool => filled($achievement['name'] ?? null))
            ->map(fn (array $achievement): array => [
                'nama_pencapaian' => $achievement['name'],
                'tahun' => $achievement['year'],
                'peringkat' => $achievement['level'],
            ])
            ->values()
            ->all();

        return [
            'player' => [
                'nama' => $validated['name'],
                'no_ic' => $validated['national_id'],
                'no_jersi' => $validated['jersey_number'],
                'umur' => $validated['age'] ?? null,
                'role_pemain' => $validated['role'],
                'position' => $validated['position'],
            ],
            'achievements' => $achievements,
        ];
    }

    /**
     * @param  list<array{nama_pencapaian: string, tahun: int, peringkat: string}>  $achievements
     */
    private function saveAchievements(Pemain $player, array $achievements): void
    {
        foreach ($achievements as $achievement) {
            $player->pencapaian()->create($achievement);
        }
    }

    private function ensurePlayerBelongsToTeam(Pasukan $pasukan, Pemain $pemain): void
    {
        abort_unless($pemain->id_pasukan === $pasukan->id, 404);
    }

    private function downloadTemplate(string $filename, string $contents): StreamedResponse
    {
        return response()->streamDownload(
            static function () use ($contents): void {
                echo $contents;
            },
            $filename,
            ['Content-Type' => 'text/csv; charset=UTF-8'],
        );
    }
}
