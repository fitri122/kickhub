<?php

namespace Database\Seeders;

use App\Models\Pasukan;
use App\Models\Perlawanan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MatchDayTwoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            $fixtures = [
                ['home' => 'KV Datuk Seri Abu Zahar Isnin', 'away' => 'KV Melaka Tengah', 'time' => '10:00:00'],
                ['home' => 'KV Kuala Klawang', 'away' => 'KV Port Dickson', 'time' => '12:00:00'],
                ['home' => 'KV Ampangan', 'away' => 'KV Juaseh', 'time' => '14:00:00'],
                ['home' => 'KV Datuk Seri Mohd Zain', 'away' => 'KV Datuk Lela Maharaja', 'time' => '16:00:00'],
            ];
            $teamNames = collect($fixtures)
                ->flatMap(fn (array $fixture): array => [$fixture['home'], $fixture['away']])
                ->unique()
                ->values();
            $teams = Pasukan::query()
                ->whereIn('nama_pasukan', $teamNames)
                ->get()
                ->keyBy('nama_pasukan');

            foreach ($teamNames as $teamName) {
                if (! $teams->has($teamName)) {
                    throw new \RuntimeException("Missing Day 2 team: {$teamName}");
                }
            }

            foreach ($fixtures as $fixture) {
                $homeTeam = $teams->get($fixture['home']);
                $awayTeam = $teams->get($fixture['away']);

                if ((int) $homeTeam->id_kumpulan !== (int) $awayTeam->id_kumpulan) {
                    throw new \RuntimeException('Day 2 fixture teams must belong to the same group.');
                }

                Perlawanan::query()->firstOrCreate(
                    [
                        'id_pasukan_1' => $homeTeam->id,
                        'id_pasukan_2' => $awayTeam->id,
                        'tempat' => 'KickHub Demo Stadium',
                    ],
                    [
                        'id_kumpulan' => $homeTeam->id_kumpulan,
                        'tarikh' => '2026-11-14',
                        'masa' => $fixture['time'],
                        'skor_pasukan_1' => null,
                        'skor_pasukan_2' => null,
                    ],
                );
            }
        });
    }
}
