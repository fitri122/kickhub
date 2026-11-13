<?php

namespace Database\Seeders;

use App\Models\Pasukan;
use App\Models\Pemain;
use Illuminate\Database\Seeder;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $playersByTeam = [
            'KV Datuk Seri Abu Zahar Isnin' => [
                'Ahmad Firdaus',
                'Danish Firdaus',
                'Faris Izzuddin',
                'Haziq Rahman',
                'Imran Syahmi',
            ],
            'KV Kuala Klawang' => [
                'Aqil Zafran',
                'Badrul Hisham',
                'Ehsan Azri',
                'Faiz Naufal',
                'Harith Luqman',
            ],
            'KV Port Dickson' => [
                'Amirul Nizam',
                'Dhiyaul Arif',
                'Fikri Danish',
                'Hakimi Zulkifli',
                'Irfan Muaz',
            ],
            'KV Melaka Tengah' => [
                'Anas Iqbal',
                'Basir Aiman',
                'Danish Azim',
                'Faris Hakimi',
                'Hilmi Fawwaz',
            ],
            'KV Ampangan' => [
                'Akmal Firdaus',
                'Ammar Syazwan',
                'Faheem Irfan',
                'Khairul Anwar',
                'Mikail Danish',
            ],
            'KV Datuk Seri Mohd Zain' => [
                'Arif Haziq',
                'Fahmi Azlan',
                'Izzat Hakim',
                'Nazmi Afiq',
                'Rizqi Fadhil',
            ],
            'KV Datuk Lela Maharaja' => [
                'Danish Khairi',
                'Hafiz Azri',
                'Iman Syakir',
                'Luqman Hakimi',
                'Syafiq Aiman',
            ],
            'KV Juaseh' => [
                'Afiq Harraz',
                'Danial Arman',
                'Faris Amsyar',
                'Haziq Firdaus',
                'Muaz Izzuddin',
            ],
        ];

        $demoPlayerNames = [
            'Aiman Zikri',
            'Adib Danish',
            'Azfar Hakim',
            'Danial Arman',
            'Faiz Haziq',
            'Farhan Luqman',
            'Harith Zikri',
            'Imran Hakim',
            'Khairul Aiman',
            'Muaz Fadhil',
            'Nazmi Firdaus',
            'Rizqi Azlan',
            'Syafiq Anwar',
        ];
        $positions = ['Goalkeeper', 'Defender', 'Midfielder', 'Midfielder', 'Forward'];

        foreach ($playersByTeam as $teamName => $playerNames) {
            $team = Pasukan::query()
                ->where('nama_pasukan', $teamName)
                ->firstOrFail();

            $playerCount = $team->pemain()->count();

            foreach ($playerNames as $index => $playerName) {
                if ($playerCount >= 18) {
                    break;
                }

                $isSpecialPlayer = $teamName === 'KV Datuk Seri Abu Zahar Isnin'
                    && $playerName === 'Ahmad Firdaus';
                $playerRole = match ($index) {
                    0 => 'Captain',
                    1 => 'Vice Captain',
                    default => 'Player',
                };

                $player = Pemain::firstOrCreate(
                    ['id_pasukan' => $team->id, 'nama' => $playerName],
                    [
                        'no_ic' => $this->uniqueDemoIdentityNumber($team->id, $index + 1),
                        'no_jersi' => $index + 1,
                        'umur' => 18 + ($index % 4),
                        'gambar' => $isSpecialPlayer ? 'images/Formal.png' : null,
                        'role_pemain' => $playerRole,
                        'position' => $positions[$index],
                    ],
                );

                if ($player->wasRecentlyCreated) {
                    $playerCount++;
                }
            }

            foreach ($demoPlayerNames as $index => $playerName) {
                if ($playerCount >= 18) {
                    break;
                }

                $jerseyNumber = $index + 6;
                $player = Pemain::firstOrCreate(
                    ['id_pasukan' => $team->id, 'nama' => sprintf('%s (Demo %d)', $playerName, $team->id)],
                    [
                        'no_ic' => $this->uniqueDemoIdentityNumber($team->id, $jerseyNumber),
                        'no_jersi' => $jerseyNumber,
                        'umur' => 18 + ($index % 4),
                        'role_pemain' => 'Demo Player',
                        'position' => $positions[($index + count($playerNames)) % count($positions)],
                    ],
                );

                if ($player->wasRecentlyCreated) {
                    $playerCount++;
                }
            }
        }
    }

    private function uniqueDemoIdentityNumber(int $teamId, int $playerNumber): string
    {
        $identityNumber = 900000000000 + ($teamId * 100) + $playerNumber;

        while (Pemain::query()->where('no_ic', (string) $identityNumber)->exists()) {
            $identityNumber++;
        }

        return (string) $identityNumber;
    }
}
