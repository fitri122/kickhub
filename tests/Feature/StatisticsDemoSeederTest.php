<?php

namespace Tests\Feature;

use App\Models\Pasukan;
use App\Models\Pemain;
use App\Models\RekodDisiplin;
use App\Models\User;
use Database\Seeders\StatisticsDemoSeeder;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticsDemoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_demo_statistics_idempotently_without_using_irfan_as_a_scorer_or_card_recipient(): void
    {
        User::factory()->create([
            'email' => 'user@gmail.com',
        ]);
        $this->seed(TeamSeeder::class);

        $this->seed(StatisticsDemoSeeder::class);
        $this->seed(StatisticsDemoSeeder::class);

        $this->assertDatabaseCount('perlawanan', 8);
        $this->assertDatabaseCount('jaringan_gol', 12);
        $this->assertDatabaseCount('rekod_disiplin', 5);
        $this->assertSame(144, Pemain::query()->count());

        foreach (Pasukan::query()->get() as $team) {
            $this->assertSame(18, $team->pemain()->count());
        }

        $irfan = Pemain::query()->where('nama', 'Irfan Iskandar bin Mohd Affandey')->first();

        if ($irfan !== null) {
            $this->assertDatabaseMissing('jaringan_gol', ['id_pemain' => $irfan->id]);
            $this->assertDatabaseMissing('rekod_disiplin', ['id_pemain' => $irfan->id]);
        }

        $this->assertSame(3, RekodDisiplin::query()->where('jenis_kad', 'kuning')->count());
        $this->assertSame(2, RekodDisiplin::query()->where('jenis_kad', 'merah')->count());
    }
}
