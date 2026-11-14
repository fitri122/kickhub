<?php

namespace Database\Seeders;

use App\Models\JaringanGol;
use App\Models\Pasukan;
use App\Models\Pemain;
use App\Models\Perlawanan;
use App\Models\RekodDisiplin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StatisticsDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $this->call(PlayerSeeder::class);

            $teamNames = [
                'KV Datuk Seri Abu Zahar Isnin',
                'KV Kuala Klawang',
                'KV Port Dickson',
                'KV Melaka Tengah',
                'KV Ampangan',
                'KV Datuk Seri Mohd Zain',
                'KV Datuk Lela Maharaja',
                'KV Juaseh',
            ];
            $teams = Pasukan::query()
                ->whereIn('nama_pasukan', $teamNames)
                ->get()
                ->keyBy('nama_pasukan');

            foreach ($teamNames as $teamName) {
                if (! $teams->has($teamName)) {
                    throw new \RuntimeException("Missing demo team: {$teamName}");
                }
            }

            $demoPlayers = Pemain::query()
                ->whereIn('id_pasukan', $teams->pluck('id'))
                ->where('role_pemain', 'Demo Player')
                ->orderBy('no_jersi')
                ->get()
                ->groupBy('id_pasukan');

            foreach ($teams as $team) {
                if ($demoPlayers->get($team->id, collect())->isEmpty()) {
                    throw new \RuntimeException("No demo players found for team {$team->nama_pasukan}");
                }
            }

            $fixtures = [
                ['home' => 0, 'away' => 1, 'home_score' => 2, 'away_score' => 1, 'goals' => [0 => 2, 1 => 1]],
                ['home' => 2, 'away' => 3, 'home_score' => 1, 'away_score' => 0, 'goals' => [2 => 1]],
                ['home' => 0, 'away' => 2, 'home_score' => 1, 'away_score' => 1, 'goals' => [0 => 1, 2 => 1]],
                ['home' => 1, 'away' => 3, 'home_score' => 2, 'away_score' => 0, 'goals' => [1 => 2]],
                ['home' => 4, 'away' => 5, 'home_score' => 1, 'away_score' => 0, 'goals' => [4 => 1]],
                ['home' => 6, 'away' => 7, 'home_score' => 1, 'away_score' => 1, 'goals' => [6 => 1, 7 => 1]],
                ['home' => 4, 'away' => 6, 'home_score' => 0, 'away_score' => 0, 'goals' => []],
                ['home' => 5, 'away' => 7, 'home_score' => 1, 'away_score' => 0, 'goals' => [5 => 1]],
            ];

            $matches = [];

            foreach ($fixtures as $index => $fixture) {
                $homeTeam = $teams->get($teamNames[$fixture['home']]);
                $awayTeam = $teams->get($teamNames[$fixture['away']]);
                $match = Perlawanan::query()->firstOrCreate(
                    [
                        'id_pasukan_1' => $homeTeam->id,
                        'id_pasukan_2' => $awayTeam->id,
                        'tempat' => 'KickHub Demo Stadium',
                    ],
                    [
                        'id_kumpulan' => $homeTeam->id_kumpulan,
                        'tarikh' => '2026-11-13',
                        'masa' => sprintf('%02d:00:00', 10 + $index),
                        'skor_pasukan_1' => $fixture['home_score'],
                        'skor_pasukan_2' => $fixture['away_score'],
                    ],
                );
                $matches[] = $match;

                foreach ($fixture['goals'] as $teamIndex => $goalCount) {
                    $team = $teams->get($teamNames[$teamIndex]);
                    $player = $demoPlayers->get($team->id)->first();
                    $existingGoals = JaringanGol::query()
                        ->where('id_perlawanan', $match->id)
                        ->where('id_pemain', $player->id)
                        ->count();

                    for ($goal = $existingGoals; $goal < $goalCount; $goal++) {
                        JaringanGol::query()->create([
                            'id_perlawanan' => $match->id,
                            'id_pemain' => $player->id,
                        ]);
                    }
                }
            }

            $cards = [
                [0, 0, 'kuning', 15],
                [1, 2, 'kuning', 35],
                [2, 2, 'merah', 60],
                [4, 4, 'kuning', 45],
                [6, 6, 'merah', 75],
            ];

            foreach ($cards as [$matchIndex, $teamIndex, $cardType, $minute]) {
                $team = $teams->get($teamNames[$teamIndex]);
                $player = $demoPlayers->get($team->id)->first();

                RekodDisiplin::query()->firstOrCreate(
                    [
                        'id_perlawanan' => $matches[$matchIndex]->id,
                        'id_pemain' => $player->id,
                        'jenis_kad' => $cardType,
                        'minit' => $minute,
                    ],
                );
            }
        });
    }
}
