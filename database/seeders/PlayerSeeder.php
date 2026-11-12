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

        $positions = ['Goalkeeper', 'Defender', 'Midfielder', 'Midfielder', 'Forward'];
        $playerNumber = 1;

        foreach ($playersByTeam as $teamName => $playerNames) {
            $team = Pasukan::query()
                ->where('nama_pasukan', $teamName)
                ->firstOrFail();

            foreach ($playerNames as $index => $playerName) {
                $isSpecialPlayer = $teamName === 'KV Datuk Seri Abu Zahar Isnin'
                    && $playerName === 'Ahmad Firdaus';
                $playerRole = match ($index) {
                    0 => 'Captain',
                    1 => 'Vice Captain',
                    default => 'Player',
                };

                Pemain::updateOrCreate(
                    ['no_ic' => sprintf('000000%06d', $playerNumber)],
                    [
                        'id_pasukan' => $team->id,
                        'nama' => $playerName,
                        'no_jersi' => $index + 1,
                        'umur' => 18 + ($index % 4),
                        'gambar' => $isSpecialPlayer ? 'images/Formal.png' : null,
                        'role_pemain' => $playerRole,
                        'position' => $positions[$index],
                    ],
                );

                $playerNumber++;
            }
        }
    }
}
