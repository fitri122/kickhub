<?php

namespace Tests\Feature;

use App\Models\Pasukan;
use App\Models\Pemain;
use App\Models\SekatanPemain;
use App\Models\User;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_dashboard_lists_registered_teams_and_links_to_details(): void
    {
        $team = Pasukan::create([
            'nama_pasukan' => 'KickHub United',
            'nama_kolej' => 'Melaka',
            'id_pengurus' => User::factory()->create()->id,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Every team. One tournament.')
            ->assertSee('KickHub United')
            ->assertSee('Melaka')
            ->assertSee(route('teams.show', $team));
    }

    public function test_dashboard_lists_all_eight_registered_teams_without_duplicates(): void
    {
        $manager = User::factory()->create();
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

        foreach ($teamNames as $teamName) {
            Pasukan::create([
                'nama_pasukan' => $teamName,
                'nama_kolej' => $teamName,
                'id_pengurus' => $manager->id,
            ]);
        }

        $response = $this->get('/')
            ->assertOk()
            ->assertSee('lg:grid-cols-4', false);

        $this->assertSame(8, substr_count($response->getContent(), 'data-team-card='));

        foreach ($teamNames as $teamName) {
            $response->assertSee($teamName);
        }
    }

    public function test_dashboard_shows_each_team_state_below_its_name(): void
    {
        User::factory()->create([
            'name' => 'Team Manager',
            'email' => 'user@gmail.com',
            'role' => User::ROLE_TEAM_MANAGER,
        ]);
        $this->seed(TeamSeeder::class);

        $response = $this->get('/')
            ->assertOk()
            ->assertSee('KV Datuk Seri Abu Zahar Isnin')
            ->assertSee('KV Kuala Klawang')
            ->assertSee('Melaka')
            ->assertSee('Negeri Sembilan');

        $this->assertSame(1, substr_count($response->getContent(), asset('images/kvdsazi.jpg')));
        $response->assertDontSee(asset('images/team-placeholder.svg'));

        $this->assertSame(3, Pasukan::query()->where('nama_kolej', 'Melaka')->count());
        $this->assertSame(5, Pasukan::query()->where('nama_kolej', 'Negeri Sembilan')->count());
    }

    public function test_team_seeder_assigns_eight_teams_to_two_groups_idempotently(): void
    {
        User::factory()->create([
            'name' => 'Team Manager',
            'email' => 'user@gmail.com',
            'role' => User::ROLE_TEAM_MANAGER,
        ]);

        $this->seed(TeamSeeder::class);
        $this->seed(TeamSeeder::class);

        $this->assertDatabaseCount('kumpulan', 2);
        $this->assertDatabaseCount('pasukan', 8);
        $this->assertDatabaseHas('kumpulan', ['nama_kumpulan' => 'Kumpulan A']);
        $this->assertDatabaseHas('kumpulan', ['nama_kumpulan' => 'Kumpulan B']);
        $this->assertSame(
            4,
            Pasukan::query()->whereHas('kumpulan', fn ($query) => $query->where('nama_kumpulan', 'Kumpulan A'))->count(),
        );
        $this->assertSame(
            4,
            Pasukan::query()->whereHas('kumpulan', fn ($query) => $query->where('nama_kumpulan', 'Kumpulan B'))->count(),
        );
        $this->assertSame(
            1,
            Pasukan::query()->where('logo', 'images/kvdsazi.jpg')->count(),
        );
        $this->assertSame(7, Pasukan::query()->whereNull('logo')->count());
        $this->assertDatabaseHas('pasukan', [
            'nama_pasukan' => 'KV Datuk Seri Abu Zahar Isnin',
            'nama_kolej' => 'Melaka',
            'logo' => 'images/kvdsazi.jpg',
        ]);
        $this->assertDatabaseHas('pasukan', [
            'nama_pasukan' => 'KV Kuala Klawang',
            'nama_kolej' => 'Negeri Sembilan',
            'logo' => null,
        ]);
    }

    public function test_team_view_page_shows_its_players_without_a_logo_placeholder(): void
    {
        $team = Pasukan::create([
            'nama_pasukan' => 'KickHub United',
            'nama_kolej' => 'Melaka',
            'id_pengurus' => User::factory()->create()->id,
        ]);
        $player = $this->createPlayer($team, 'Team Player');
        $otherTeam = Pasukan::create([
            'nama_pasukan' => 'Other Team',
            'nama_kolej' => 'Negeri Sembilan',
            'id_pengurus' => User::factory()->create()->id,
        ]);
        $this->createPlayer($otherTeam, 'Player From Another Team');

        $this->get(route('teams.show', $team))
            ->assertOk()
            ->assertSee('KickHub United')
            ->assertSee('Melaka')
            ->assertSee('Team Player')
            ->assertSee((string) $player->no_jersi)
            ->assertSee((string) $player->umur)
            ->assertSee('Active')
            ->assertDontSee('Player From Another Team')
            ->assertSee('players-tab', false)
            ->assertSee('coaches-tab', false)
            ->assertSee('Ahmad Firdaus')
            ->assertSee('Hafiz Rahman')
            ->assertSee('Daniel Zulkifli')
            ->assertDontSee('Inactive')
            ->assertSee('data-coach-name="Ahmad Firdaus"', false)
            ->assertSee('data-coach-age="45"', false)
            ->assertSee('data-player-name="Team Player"', false)
            ->assertSee('data-player-age="20"', false)
            ->assertSee('data-player-role="Pemain Biasa"', false)
            ->assertSee('Position')
            ->assertSee('Achievements')
            ->assertDontSee('images/Formal.png')
            ->assertDontSee(asset('images/team-placeholder.svg'));
    }

    public function test_player_status_is_blacklist_when_a_sekatan_record_exists(): void
    {
        $team = Pasukan::create([
            'nama_pasukan' => 'KickHub United',
            'nama_kolej' => 'Melaka',
            'id_pengurus' => User::factory()->create()->id,
        ]);
        $player = $this->createPlayer($team, 'Blacklisted Player');
        SekatanPemain::create([
            'id_pemain' => $player->id,
            'status' => 'Blacklisted',
        ]);

        $this->get(route('teams.show', $team))
            ->assertOk()
            ->assertSee('Blacklisted Player')
            ->assertSee('Blacklist');
    }

    public function test_team_players_are_paginated(): void
    {
        $team = Pasukan::create([
            'nama_pasukan' => 'KickHub United',
            'nama_kolej' => 'Melaka',
            'id_pengurus' => User::factory()->create()->id,
        ]);

        foreach (range(1, 16) as $number) {
            $this->createPlayer($team, sprintf('Player %02d', $number));
        }

        $this->get(route('teams.show', $team))
            ->assertOk()
            ->assertSee('Player 01')
            ->assertSee('Player 15')
            ->assertDontSee('Player 16')
            ->assertSee('Next');

        $this->get(route('teams.show', ['pasukan' => $team, 'page' => 2]))
            ->assertOk()
            ->assertSee('Player 16')
            ->assertDontSee('Player 01');
    }

    public function test_dashboard_uses_a_team_logo_when_one_is_available(): void
    {
        $user = User::factory()->create();
        Pasukan::create([
            'nama_pasukan' => 'KickHub United',
            'nama_kolej' => 'Melaka',
            'id_pengurus' => $user->id,
            'logo' => 'team-logos/kickhub-united.png',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee(asset('storage/team-logos/kickhub-united.png'));
    }

    public function test_authenticated_dashboard_loads_registered_teams(): void
    {
        $user = User::factory()->create();
        Pasukan::create([
            'nama_pasukan' => 'KickHub United',
            'nama_kolej' => 'Melaka',
            'id_pengurus' => $user->id,
        ]);

        $this->actingAs($user)->get(route('dashboard'))
            ->assertOk()
            ->assertSee('KickHub United');
    }

    private function createPlayer(Pasukan $team, string $name): Pemain
    {
        return Pemain::create([
            'id_pasukan' => $team->id,
            'nama' => $name,
            'no_ic' => fake()->unique()->numerify('############'),
            'no_jersi' => fake()->numberBetween(1, 99),
            'umur' => 20,
            'role_pemain' => 'Pemain Biasa',
            'position' => 'Forward',
        ]);
    }
}
