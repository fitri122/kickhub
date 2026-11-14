<?php

namespace App\Http\Controllers;

use App\Models\JaringanGol;
use App\Models\Perlawanan;
use App\Models\RekodDisiplin;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class MatchScheduleController extends Controller
{
    public function index(): View
    {
        $matches = Perlawanan::query()
            ->with(['kumpulan', 'pasukan1', 'pasukan2'])
            ->orderBy('tarikh')
            ->orderBy('masa')
            ->orderBy('id')
            ->get();

        $dates = $matches
            ->pluck('tarikh')
            ->map(fn (Carbon $date): string => $date->toDateString())
            ->unique()
            ->values();
        $dayByDate = $dates
            ->mapWithKeys(fn (string $date, int $index): array => [$date => min($index + 1, 3)])
            ->all();
        $dayCounts = ['1' => 0, '2' => 0, '3' => 0];

        foreach ($matches as $match) {
            $date = $match->tarikh->toDateString();
            $day = (string) ($dayByDate[$date] ?? 3);
            $isCompleted = $match->skor_pasukan_1 !== null && $match->skor_pasukan_2 !== null;

            $match->setAttribute('schedule_day', $day);
            $match->setAttribute('is_completed', $isCompleted);
            $match->setAttribute('display_group', $this->groupLabel($match->kumpulan?->nama_kumpulan));
            $match->setAttribute('display_time', $this->formatKickoff($match->masa));
            $dayCounts[$day]++;
        }

        return view('schedule', compact('matches', 'dayCounts'));
    }

    public function show(Perlawanan $perlawanan): View
    {
        $perlawanan->load([
            'kumpulan',
            'pasukan1',
            'pasukan2',
            'jaringanGol.pemain.pasukan',
            'rekodDisiplin.pemain.pasukan',
        ]);

        $isCompleted = $perlawanan->skor_pasukan_1 !== null && $perlawanan->skor_pasukan_2 !== null;
        $goalScorers = $perlawanan->jaringanGol
            ->groupBy('id_pemain')
            ->map(function (Collection $goals): array {
                $goal = $goals->first();

                return [
                    'player_name' => $goal->pemain->nama,
                    'team_name' => $goal->pemain->pasukan->nama_pasukan,
                    'goals' => $goals->count(),
                ];
            })
            ->sortBy('player_name')
            ->values();

        $events = $perlawanan->jaringanGol
            ->map(fn (JaringanGol $goal): array => [
                'id' => $goal->id,
                'type' => 'goal',
                'minute' => null,
                'player_name' => $goal->pemain->nama,
                'team_name' => $goal->pemain->pasukan->nama_pasukan,
            ])
            ->concat($perlawanan->rekodDisiplin->map(fn (RekodDisiplin $record): array => [
                'id' => $record->id,
                'type' => $record->jenis_kad === 'kuning' ? 'yellow-card' : 'red-card',
                'minute' => (int) $record->minit,
                'player_name' => $record->pemain->nama,
                'team_name' => $record->pemain->pasukan->nama_pasukan,
            ]))
            ->sort(function (array $left, array $right): int {
                $minuteOrder = ($left['minute'] ?? PHP_INT_MAX) <=> ($right['minute'] ?? PHP_INT_MAX);

                return $minuteOrder !== 0 ? $minuteOrder : ($left['id'] <=> $right['id']);
            })
            ->values();

        return view('matches.show', [
            'match' => $perlawanan,
            'isCompleted' => $isCompleted,
            'statusLabel' => $isCompleted ? __('Completed') : __('Upcoming'),
            'groupLabel' => $this->groupLabel($perlawanan->kumpulan?->nama_kumpulan),
            'kickoffTime' => $this->formatKickoff($perlawanan->masa),
            'goalScorers' => $goalScorers,
            'events' => $events,
        ]);
    }

    private function groupLabel(?string $groupName): string
    {
        return $groupName === null
            ? __('Group not assigned')
            : str_replace('Kumpulan ', 'Group ', $groupName);
    }

    private function formatKickoff(string $time): string
    {
        return Carbon::createFromFormat('H:i:s', $time)->format('g:i A');
    }
}
