<?php

namespace App\Http\Controllers;

use App\Models\JaringanGol;
use App\Models\Pasukan;
use App\Models\Perlawanan;
use App\Models\RekodDisiplin;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(): View
    {
        $teams = Pasukan::query()
            ->orderBy('nama_pasukan')
            ->get(['id', 'nama_pasukan']);

        $cleanSheetCounts = $teams
            ->mapWithKeys(fn (Pasukan $team): array => [$team->id => 0])
            ->all();

        $completedMatches = Perlawanan::query()
            ->whereNotNull('skor_pasukan_1')
            ->whereNotNull('skor_pasukan_2')
            ->get(['id_pasukan_1', 'id_pasukan_2', 'skor_pasukan_1', 'skor_pasukan_2']);

        foreach ($completedMatches as $match) {
            if ((int) $match->skor_pasukan_2 === 0) {
                $cleanSheetCounts[$match->id_pasukan_1]++;
            }

            if ((int) $match->skor_pasukan_1 === 0) {
                $cleanSheetCounts[$match->id_pasukan_2]++;
            }
        }

        $cleanSheets = [];

        foreach ($teams as $team) {
            $cleanSheetCount = $cleanSheetCounts[$team->id];

            if ($cleanSheetCount > 0) {
                $cleanSheets[] = [
                    'team_name' => $team->nama_pasukan,
                    'clean_sheets' => $cleanSheetCount,
                ];
            }
        }

        usort($cleanSheets, static fn (array $left, array $right): int => ($right['clean_sheets'] <=> $left['clean_sheets'])
            ?: strcasecmp($left['team_name'], $right['team_name']));

        $topScorers = JaringanGol::query()
            ->join('pemain', 'pemain.id', '=', 'jaringan_gol.id_pemain')
            ->join('pasukan', 'pasukan.id', '=', 'pemain.id_pasukan')
            ->select([
                'pemain.id as player_id',
                'pemain.nama as player_name',
                'pasukan.nama_pasukan as team_name',
            ])
            ->selectRaw('COUNT(jaringan_gol.id) AS score')
            ->groupBy('pemain.id', 'pemain.nama', 'pasukan.nama_pasukan')
            ->orderByDesc('score')
            ->orderBy('pemain.nama')
            ->limit(3)
            ->get();

        return view('statistics', [
            'totalMatches' => Perlawanan::query()->count(),
            'totalScorers' => JaringanGol::query()->distinct()->count('id_pemain'),
            'yellowCardTotal' => RekodDisiplin::query()->where('jenis_kad', 'kuning')->count(),
            'redCardTotal' => RekodDisiplin::query()->where('jenis_kad', 'merah')->count(),
            'topScorers' => $topScorers,
            'cleanSheets' => array_slice($cleanSheets, 0, 3),
        ]);
    }

    public function yellowCards(): View
    {
        return $this->renderCardList('kuning', __('Yellow Card'));
    }

    public function redCards(): View
    {
        return $this->renderCardList('merah', __('Red Card'));
    }

    private function renderCardList(string $cardType, string $cardTitle): View
    {
        $cardPlayers = RekodDisiplin::query()
            ->join('pemain', 'pemain.id', '=', 'rekod_disiplin.id_pemain')
            ->join('pasukan', 'pasukan.id', '=', 'pemain.id_pasukan')
            ->where('rekod_disiplin.jenis_kad', $cardType)
            ->select([
                'pemain.id as player_id',
                'pemain.nama as player_name',
                'pasukan.nama_pasukan as team_name',
            ])
            ->selectRaw('COUNT(rekod_disiplin.id) AS card_count')
            ->groupBy('pemain.id', 'pemain.nama', 'pasukan.nama_pasukan')
            ->orderByDesc('card_count')
            ->orderBy('pemain.nama')
            ->get();

        return view('disciplinary-cards', [
            'cardTitle' => $cardTitle,
            'totalCards' => RekodDisiplin::query()->where('jenis_kad', $cardType)->count(),
            'cardPlayers' => $cardPlayers,
            'isYellowCard' => $cardType === 'kuning',
        ]);
    }
}
