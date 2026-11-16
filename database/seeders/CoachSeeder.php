<?php

namespace Database\Seeders;

use App\Models\Jurulatih;
use App\Models\Pasukan;
use Illuminate\Database\Seeder;

class CoachSeeder extends Seeder
{
    public function run(): void
    {
        $coaches = [
            ['nama' => 'Ahmad Firdaus', 'umur' => 45],
            ['nama' => 'Hafiz Rahman', 'umur' => 39],
            ['nama' => 'Daniel Zulkifli', 'umur' => 42],
            ['nama' => 'Rizal Hakim', 'umur' => 41],
            ['nama' => 'Azmi Khalid', 'umur' => 36],
        ];

        foreach (Pasukan::query()->get() as $team) {
            foreach ($coaches as $coach) {
                Jurulatih::firstOrCreate(
                    ['id_pasukan' => $team->id, 'nama' => $coach['nama']],
                    ['umur' => $coach['umur']],
                );
            }
        }
    }
}
