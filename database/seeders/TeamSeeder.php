<?php

namespace Database\Seeders;

use App\Models\Kumpulan;
use App\Models\Pasukan;
use App\Models\User;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = User::query()
            ->where('email', 'user@gmail.com')
            ->firstOrFail();

        $groups = [
            'Kumpulan A' => Kumpulan::firstOrCreate(['nama_kumpulan' => 'Kumpulan A']),
            'Kumpulan B' => Kumpulan::firstOrCreate(['nama_kumpulan' => 'Kumpulan B']),
        ];

        $teams = [
            'Kumpulan A' => [
                'KV Datuk Seri Abu Zahar Isnin' => 'Melaka',
                'KV Kuala Klawang' => 'Negeri Sembilan',
                'KV Port Dickson' => 'Negeri Sembilan',
                'KV Melaka Tengah' => 'Melaka',
            ],
            'Kumpulan B' => [
                'KV Ampangan' => 'Negeri Sembilan',
                'KV Datuk Seri Mohd Zain' => 'Melaka',
                'KV Datuk Lela Maharaja' => 'Negeri Sembilan',
                'KV Juaseh' => 'Negeri Sembilan',
            ],
        ];

        foreach ($teams as $groupName => $teamsInGroup) {
            foreach ($teamsInGroup as $teamName => $state) {
                Pasukan::updateOrCreate(
                    ['nama_pasukan' => $teamName],
                    [
                        'nama_kolej' => $state,
                        'id_pengurus' => $manager->id,
                        'id_kumpulan' => $groups[$groupName]->id,
                        'logo' => 'images/kvdsazi.jpg',
                    ],
                );
            }
        }
    }
}
