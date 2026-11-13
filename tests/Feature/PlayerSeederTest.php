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

    public function test_it_seeds_eighteen_unique_players_per_team_idempotently_without_sekatan_records(): void
    {
        User::factory()->create([
            'email' => 'user@gmail.com',
        ]);
        $this->seed(TeamSeeder::class);

        $this->seed(PlayerSeeder::class);
        $this->seed(PlayerSeeder::class);

        $this->assertSame(8, Pasukan::query()->count());
        $this->assertSame(144, Pemain::query()->count());
        $this->assertSame(144, Pemain::query()->distinct()->count('nama'));
        $this->assertSame(144, Pemain::query()->distinct()->count('no_ic'));
        $this->assertDatabaseHas('pemain', [
            'nama' => 'Ahmad Firdaus',
            'gambar' => 'images/Formal.png',
        ]);
        $this->assertSame(1, Pemain::query()->whereNotNull('gambar')->count());

        foreach (Pasukan::query()->get() as $team) {
            $this->assertSame(18, $team->pemain()->count());
            $this->assertSame(1, $team->pemain()->where('role_pemain', 'Captain')->count());
            $this->assertSame(1, $team->pemain()->where('role_pemain', 'Vice Captain')->count());
            $this->assertSame(3, $team->pemain()->where('role_pemain', 'Player')->count());
        }

        $this->assertDatabaseCount('sekatan_pemain', 0);
    }

    public function test_it_preserves_existing_irfan_record_while_filling_his_team_roster(): void
    {
        User::factory()->create([
            'email' => 'user@gmail.com',
        ]);
        $this->seed(TeamSeeder::class);
        $team = Pasukan::query()->where('nama_pasukan', 'KV Datuk Seri Abu Zahar Isnin')->firstOrFail();
        $irfan = Pemain::create([
            'id_pasukan' => $team->id,
            'nama' => 'Irfan Iskandar bin Mohd Affandey',
            'no_ic' => fake()->unique()->numerify('############'),
            'no_jersi' => 25,
            'umur' => 19,
            'role_pemain' => 'Captain',
            'position' => 'Goalkeeper',
        ]);

        $this->seed(PlayerSeeder::class);

        $this->assertDatabaseHas('pemain', [
            'id' => $irfan->id,
            'id_pasukan' => $team->id,
            'nama' => 'Irfan Iskandar bin Mohd Affandey',
            'no_jersi' => 25,
            'umur' => 19,
            'role_pemain' => 'Captain',
            'position' => 'Goalkeeper',
        ]);
        $this->assertSame(18, $team->pemain()->count());
    }
}
