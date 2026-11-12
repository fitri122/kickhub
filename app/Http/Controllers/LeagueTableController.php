<?php

namespace App\Http\Controllers;

use App\Models\Kumpulan;
use App\Models\Perlawanan;
use App\Models\Pasukan;
use App\Models\RekodDisiplin;
use Illuminate\View\View;

class LeagueTableController extends Controller
{
    public function overall(): View
    {
        return $this->render('overall');
    }

    public function groups(): View
    {
        return $this->render('groups');
    }

    private function render(string $page): View
    {
        $teams = Pasukan::query()
            ->with('kumpulan:id,nama_kumpulan')
            ->orderBy('nama_pasukan')
            ->get(['id', 'nama_pasukan', 'id_kumpulan']);

        $standingsByTeam = [];

        foreach ($teams as $team) {
            $groupName = $team->kumpulan?->nama_kumpulan;
            $groupCode = $groupName === null
                ? '—'
                : trim(str_ireplace(['Kumpulan ', 'Group '], '', $groupName));

            $standingsByTeam[$team->id] = [
                'team_id' => $team->id,
                'team_name' => $team->nama_pasukan,
                'group_id' => $team->id_kumpulan,
                'group_label' => $groupName === null ? '—' : 'Group '.$groupCode,
                'group_code' => $groupCode,
                'played' => 0,
                'wins' => 0,
                'draws' => 0,
                'losses' => 0,
                'goals_for' => 0,
                'goals_against' => 0,
                'goal_difference' => 0,
                'yellow_cards' => 0,
                'red_cards' => 0,
                'points' => 0,
            ];
        }

        $recordResult = static function (array &$standing, int $goalsFor, int $goalsAgainst): void {
            $standing['played']++;
            $standing['goals_for'] += $goalsFor;
            $standing['goals_against'] += $goalsAgainst;
            $standing['goal_difference'] = $standing['goals_for'] - $standing['goals_against'];

            if ($goalsFor > $goalsAgainst) {
                $standing['wins']++;
                $standing['points'] += 3;
            } elseif ($goalsFor === $goalsAgainst) {
                $standing['draws']++;
                $standing['points']++;
            } else {
                $standing['losses']++;
            }
        };

        $completedMatches = Perlawanan::query()
            ->whereNotNull('skor_pasukan_1')
            ->whereNotNull('skor_pasukan_2')
            ->get(['id_pasukan_1', 'id_pasukan_2', 'skor_pasukan_1', 'skor_pasukan_2']);

        foreach ($completedMatches as $match) {
            $goalsForTeam1 = (int) $match->skor_pasukan_1;
            $goalsForTeam2 = (int) $match->skor_pasukan_2;

            $recordResult($standingsByTeam[$match->id_pasukan_1], $goalsForTeam1, $goalsForTeam2);
            $recordResult($standingsByTeam[$match->id_pasukan_2], $goalsForTeam2, $goalsForTeam1);
        }

        $teamCards = RekodDisiplin::query()
            ->join('pemain', 'pemain.id', '=', 'rekod_disiplin.id_pemain')
            ->select('pemain.id_pasukan')
            ->selectRaw("SUM(CASE WHEN rekod_disiplin.jenis_kad = 'kuning' THEN 1 ELSE 0 END) AS yellow_cards")
            ->selectRaw("SUM(CASE WHEN rekod_disiplin.jenis_kad = 'merah' THEN 1 ELSE 0 END) AS red_cards")
            ->groupBy('pemain.id_pasukan')
            ->get();

        foreach ($teamCards as $teamCard) {
            if (isset($standingsByTeam[$teamCard->id_pasukan])) {
                $standingsByTeam[$teamCard->id_pasukan]['yellow_cards'] = (int) $teamCard->yellow_cards;
                $standingsByTeam[$teamCard->id_pasukan]['red_cards'] = (int) $teamCard->red_cards;
            }
        }

        $sortStandings = static function (array $standings): array {
            usort($standings, static function (array $left, array $right): int {
                return ($right['points'] <=> $left['points'])
                    ?: ($right['goal_difference'] <=> $left['goal_difference'])
                    ?: ($right['goals_for'] <=> $left['goals_for'])
                    ?: ($right['wins'] <=> $left['wins'])
                    ?: strcasecmp($left['team_name'], $right['team_name']);
            });

            foreach ($standings as $index => &$standing) {
                $standing['rank'] = $index + 1;
            }

            return $standings;
        };

        $overallTable = [
            'id' => 'overall-standings',
            'title' => __('Overall League Standings'),
            'is_overall' => true,
            'group_code' => null,
            'standings' => $sortStandings(array_values($standingsByTeam)),
        ];

        $groupTables = [];
        $groups = Kumpulan::query()
            ->orderBy('nama_kumpulan')
            ->get(['id', 'nama_kumpulan']);

        foreach ($groups as $group) {
            $groupCode = trim(str_ireplace(['Kumpulan ', 'Group '], '', $group->nama_kumpulan));
            $groupStandings = array_filter(
                $standingsByTeam,
                fn (array $standing): bool => $standing['group_id'] === $group->id,
            );

            $groupTables[] = [
                'id' => 'group-'.$group->id,
                'title' => __('Group :group', ['group' => $groupCode]),
                'is_overall' => false,
                'group_code' => $groupCode,
                'standings' => $sortStandings(array_values($groupStandings)),
            ];
        }

        return view('league-table', [
            'activePage' => $page,
            'hasGroupColumn' => $page === 'overall',
            'tables' => $page === 'overall' ? [$overallTable] : $groupTables,
        ]);
    }
}
