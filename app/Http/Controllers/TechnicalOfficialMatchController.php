<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveMatchRequest;
use App\Models\JaringanGol;
use App\Models\Kumpulan;
use App\Models\Pasukan;
use App\Models\Pemain;
use App\Models\Perlawanan;
use App\Models\RekodDisiplin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class TechnicalOfficialMatchController extends Controller
{
    public function index(): View
    {
        $matches = Perlawanan::query()
            ->with(['kumpulan', 'pasukan1', 'pasukan2'])
            ->orderBy('tarikh')
            ->orderBy('masa')
            ->orderBy('id')
            ->paginate(20);

        return view('technical-official.matches.index', compact('matches'));
    }

    public function create(): View
    {
        return $this->formView(null);
    }

    public function store(SaveMatchRequest $request): RedirectResponse
    {
        $match = DB::transaction(
            fn (): Perlawanan => $this->saveMatch(new Perlawanan, $request->validated()),
        );

        return redirect()
            ->route('technical-official.matches.edit', $match)
            ->with('status', __('Match created successfully.'));
    }

    public function edit(Perlawanan $perlawanan): View
    {
        return $this->formView($perlawanan);
    }

    public function update(SaveMatchRequest $request, Perlawanan $perlawanan): RedirectResponse
    {
        $match = DB::transaction(
            fn (): Perlawanan => $this->saveMatch($perlawanan, $request->validated()),
        );

        return redirect()
            ->route('technical-official.matches.edit', $match)
            ->with('status', __('Match updated successfully.'));
    }

    public function destroy(Perlawanan $perlawanan): RedirectResponse
    {
        $perlawanan->delete();

        return redirect()
            ->route('technical-official.matches.index')
            ->with('status', __('Match and its recorded goals and cards deleted successfully.'));
    }

    public function destroyAll(): RedirectResponse
    {
        DB::transaction(function (): void {
            Perlawanan::query()
                ->orderBy('id')
                ->chunkById(100, function ($matches): void {
                    $matches->each(fn (Perlawanan $match) => $match->delete());
                });
        });

        return redirect()
            ->route('technical-official.matches.index')
            ->with('status', __('All matches and their recorded goals and cards deleted successfully.'));
    }

    private function formView(?Perlawanan $match): View
    {
        if ($match !== null) {
            $match->load(['jaringanGol', 'rekodDisiplin']);
        }

        $groups = Kumpulan::query()
            ->with('pasukan:id,id_kumpulan,nama_pasukan')
            ->orderBy('nama_kumpulan')
            ->get(['id', 'nama_kumpulan']);

        $teamsByGroup = $groups
            ->mapWithKeys(fn (Kumpulan $group): array => [
                $group->id => $group->pasukan
                    ->map(fn (Pasukan $team): array => [
                        'id' => (int) $team->id,
                        'name' => $team->nama_pasukan,
                    ])
                    ->values()
                    ->all(),
            ])
            ->all();

        $teamIds = $groups
            ->flatMap(fn (Kumpulan $group) => $group->pasukan->pluck('id'))
            ->all();

        $playersByTeam = Pemain::query()
            ->whereIn('id_pasukan', $teamIds)
            ->orderBy('nama')
            ->get(['id', 'id_pasukan', 'nama', 'no_jersi'])
            ->groupBy('id_pasukan')
            ->map(fn ($players) => $players
                ->map(fn (Pemain $player): array => [
                    'id' => (int) $player->id,
                    'name' => $player->nama,
                    'jersey' => (int) $player->no_jersi,
                ])
                ->values()
                ->all())
            ->all();

        $goalScorers = old(
            'goal_scorers',
            $match?->jaringanGol
                ->map(fn (JaringanGol $goal): string => (string) $goal->id_pemain)
                ->all() ?? [],
        );
        $goalScorers = is_array($goalScorers)
            ? array_values(array_map(fn (mixed $playerId): string => $this->scalarString($playerId), $goalScorers))
            : [];

        $cards = old(
            'cards',
            $match?->rekodDisiplin
                ->map(fn (RekodDisiplin $record): array => [
                    'player_id' => (string) $record->id_pemain,
                    'jenis_kad' => $record->jenis_kad,
                    'minit' => (string) $record->minit,
                    'catatan' => $record->catatan ?? '',
                ])
                ->all() ?? [],
        );
        $cards = is_array($cards)
            ? array_values(array_filter($cards, 'is_array'))
            : [];
        $cards = array_map(fn (array $card): array => [
            'player_id' => $this->scalarString($card['player_id'] ?? ''),
            'jenis_kad' => $this->scalarString($card['jenis_kad'] ?? 'kuning'),
            'minit' => $this->scalarString($card['minit'] ?? ''),
            'catatan' => $this->scalarString($card['catatan'] ?? ''),
        ], $cards);

        $groupId = old('id_kumpulan', $match?->id_kumpulan ?? '');
        $homeTeamId = old('id_pasukan_1', $match?->id_pasukan_1 ?? '');
        $awayTeamId = old('id_pasukan_2', $match?->id_pasukan_2 ?? '');

        return view('technical-official.matches.form', [
            'match' => $match,
            'groups' => $groups,
            'teamsByGroup' => $teamsByGroup,
            'playersByTeam' => $playersByTeam,
            'selectedGroupId' => $this->scalarString($groupId),
            'selectedHomeTeamId' => $this->scalarString($homeTeamId),
            'selectedAwayTeamId' => $this->scalarString($awayTeamId),
            'completed' => filter_var(
                old('is_completed', $match !== null
                    && $match->skor_pasukan_1 !== null
                    && $match->skor_pasukan_2 !== null),
                FILTER_VALIDATE_BOOLEAN,
            ),
            'goalScorers' => $goalScorers,
            'cards' => $cards,
            'matchLabelValue' => $this->scalarString(old('match_label', $match?->match_label ?? '')),
            'dateValue' => $this->scalarString(old('tarikh', $match?->tarikh?->toDateString() ?? '')),
            'timeValue' => $this->scalarString(old('masa', $match === null ? '' : substr($match->masa, 0, 5))),
            'venueValue' => $this->scalarString(old('tempat', $match?->tempat ?? '')),
        ]);
    }

    private function saveMatch(Perlawanan $match, array $data): Perlawanan
    {
        $completed = filter_var($data['is_completed'] ?? false, FILTER_VALIDATE_BOOLEAN);
        $goalScorers = $completed
            ? array_map('intval', $data['goal_scorers'] ?? [])
            : [];
        $cards = $completed ? ($data['cards'] ?? []) : [];
        $playerTeams = Pemain::query()
            ->whereIn('id', $goalScorers)
            ->pluck('id_pasukan', 'id');

        $homeTeamId = (int) $data['id_pasukan_1'];
        $awayTeamId = (int) $data['id_pasukan_2'];
        $homeScore = 0;
        $awayScore = 0;

        foreach ($goalScorers as $playerId) {
            $teamId = (int) $playerTeams->get($playerId);

            if ($teamId === $homeTeamId) {
                $homeScore++;
            } elseif ($teamId === $awayTeamId) {
                $awayScore++;
            }
        }

        $match->fill([
            'match_label' => $data['match_label'] ?? null,
            'id_kumpulan' => (int) $data['id_kumpulan'],
            'id_pasukan_1' => $homeTeamId,
            'id_pasukan_2' => $awayTeamId,
            'tarikh' => $data['tarikh'],
            'masa' => $data['masa'].':00',
            'tempat' => $data['tempat'],
            'skor_pasukan_1' => $completed ? $homeScore : null,
            'skor_pasukan_2' => $completed ? $awayScore : null,
        ])->save();

        $match->jaringanGol()->delete();
        $match->rekodDisiplin()
            ->get()
            ->each(fn (RekodDisiplin $record) => $record->delete());

        foreach ($goalScorers as $playerId) {
            $match->jaringanGol()->create(['id_pemain' => $playerId]);
        }

        foreach ($cards as $card) {
            $match->rekodDisiplin()->create([
                'id_pemain' => (int) $card['player_id'],
                'jenis_kad' => $card['jenis_kad'],
                'minit' => (int) $card['minit'],
                'catatan' => $card['catatan'] ?? null,
            ]);
        }

        return $match;
    }

    private function scalarString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
