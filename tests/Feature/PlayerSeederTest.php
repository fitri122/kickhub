<?php

namespace Tests\Feature;

use App\Models\Pasukan;
use App\Models\Pemain;
use App\Models\User;
use Database\Seeders\PlayerSeeder;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_five_unique_players_per_team_idempotently_without_sekatan_records(): void
    {
        User::factory()->create([
            'email' => 'user@gmail.com',
        ]);
        $this->seed(TeamSeeder::class);

        $this->seed(PlayerSeeder::class);
        $this->seed(PlayerSeeder::class);

        $this->assertSame(8, Pasukan::query()->count());
        $this->assertSame(40, Pemain::query()->count());
        $this->assertSame(40, Pemain::query()->distinct()->count('nama'));
        $this->assertSame(40, Pemain::query()->distinct()->count('no_ic'));
        $this->assertDatabaseHas('pemain', [
            'nama' => 'Ahmad Firdaus',
            'gambar' => 'images/Formal.png',
        ]);
        $this->assertSame(1, Pemain::query()->whereNotNull('gambar')->count());

        foreach (Pasukan::query()->get() as $team) {
            $this->assertSame(5, $team->pemain()->count());
            $this->assertSame(1, $team->pemain()->where('role_pemain', 'Captain')->count());
            $this->assertSame(1, $team->pemain()->where('role_pemain', 'Vice Captain')->count());
            $this->assertSame(3, $team->pemain()->where('role_pemain', 'Player')->count());
        }

        $this->assertDatabaseCount('sekatan_pemain', 0);
    }
}
