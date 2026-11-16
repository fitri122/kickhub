<?php

namespace Tests\Feature;

use App\Models\Jurulatih;
use App\Models\Pasukan;
use App\Models\Pemain;
use App\Models\Perlawanan;
use App\Models\RekodDisiplin;
use App\Models\SekatanPemain;
use App\Models\User;
use App\Services\PlayerSuspensionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerSuspensionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_technical_official_dashboard_links_to_groups_matches_and_player_suspension(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);

        $this->actingAs($official)
            ->get(route('technical-official.dashboard'))
            ->assertOk()
            ->assertSee('Groups')
            ->assertSee('Matches')
            ->assertSee('Player Suspension')
            ->assertSee('href="'.route('league-table').'"', false)
            ->assertSee('href="'.route('schedule').'"', false)
            ->assertSee('href="'.route('technical-official.player-suspensions.index').'"', false);
    }

    public function test_technical_official_can_choose_a_team_and_view_only_its_players(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $team = $this->createTeam($official, 'KickHub United');
        $otherTeam = $this->createTeam($official, 'Opponent');
        $blockedPlayer = $this->createPlayer($team, 'Blocked Player');
        $activePlayer = $this->createPlayer($team, 'Active Player');
        $otherTeamPlayer = $this->createPlayer($otherTeam, 'Other Team Player');
        $blockedPlayer->manualSekatan()->create([
            'status' => SekatanPemain::STATUS_BLOCKED,
            'jenis_sekatan' => SekatanPemain::TYPE_MANUAL,
        ]);

        $this->actingAs($official)
            ->get(route('technical-official.player-suspensions.index'))
            ->assertOk()
            ->assertSee('Player Suspension')
            ->assertSee('KickHub United')
            ->assertSee('Opponent')
            ->assertSee(route('technical-official.player-suspensions.teams.show', $team))
            ->assertDontSee('Blocked Player');

        $this->get(route('technical-official.player-suspensions.teams.show', $team))
            ->assertOk()
            ->assertSee('role="dialog"', false)
            ->assertSee('aria-modal="true"', false)
            ->assertSee('backdrop-blur-md', false)
            ->assertSee('Blocked Player')
            ->assertSee('Active Player')
            ->assertSee('Blocked')
            ->assertSee('Active')
            ->assertSee('aria-label="Activate Blocked Player"', false)
            ->assertSee('aria-label="Block Active Player"', false)
            ->assertDontSee($otherTeamPlayer->nama)
            ->assertDontSee('Activate Player')
            ->assertDontSee('Action');
    }

    public function test_technical_official_player_page_escapes_team_and_player_names(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $team = $this->createTeam($official, 'Team <script>alert(1)</script>');
        $this->createPlayer($team, 'Player <script>alert(1)</script>');

        $response = $this->actingAs($official)
            ->get(route('technical-official.player-suspensions.teams.show', $team))
            ->assertOk();

        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false);
        $response->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_technical_official_can_block_a_player_from_matches(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $team = $this->createTeam($official, 'KickHub United');
        $opponent = $this->createTeam($official, 'Opponent');
        $player = $this->createPlayer($team, 'Test Player');
        $match = $this->createMatch($team, $opponent);

        $this->actingAs($official)
            ->from(route('technical-official.player-suspensions.teams.show', $team))
            ->post(route('technical-official.player-suspensions.store', $player))
            ->assertRedirect(route('technical-official.player-suspensions.teams.show', $team))
            ->assertSessionHas('status', 'Player blocked successfully.');

        $this->assertDatabaseHas('sekatan_pemain', [
            'id_pemain' => $player->id,
            'jenis_sekatan' => SekatanPemain::TYPE_MANUAL,
            'status' => SekatanPemain::STATUS_BLOCKED,
        ]);
        $this->assertFalse(app(PlayerSuspensionService::class)->isEligibleForMatch($player, $match));

        $this->get(route('technical-official.player-suspensions.teams.show', $team))
            ->assertOk()
            ->assertSee('aria-label="Activate Test Player"', false);
        $this->get(route('teams.show', $team))
            ->assertOk()
            ->assertSee('Test Player')
            ->assertSee('Blocked');
    }

    public function test_technical_official_can_activate_a_player_without_removing_disciplinary_suspensions(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $team = $this->createTeam($official, 'KickHub United');
        $player = $this->createPlayer($team, 'Test Player');
        $player->manualSekatan()->create([
            'status' => SekatanPemain::STATUS_BLOCKED,
            'jenis_sekatan' => SekatanPemain::TYPE_MANUAL,
        ]);
        $disciplinarySuspension = $player->suspensions()->create([
            'status' => SekatanPemain::STATUS_PENDING,
            'jenis_sekatan' => SekatanPemain::TYPE_DISCIPLINARY,
        ]);

        $this->actingAs($official)
            ->from(route('technical-official.player-suspensions.teams.show', $team))
            ->delete(route('technical-official.player-suspensions.destroy', $player))
            ->assertRedirect(route('technical-official.player-suspensions.teams.show', $team))
            ->assertSessionHas('status', 'Player activated successfully.');

        $this->assertDatabaseMissing('sekatan_pemain', [
            'id_pemain' => $player->id,
            'jenis_sekatan' => SekatanPemain::TYPE_MANUAL,
        ]);
        $this->assertModelExists($disciplinarySuspension);
        $this->get(route('technical-official.player-suspensions.teams.show', $team))
            ->assertOk()
            ->assertSee('aria-label="Block Test Player"', false);
        $this->get(route('teams.show', $team))
            ->assertOk()
            ->assertSee('Test Player')
            ->assertDontSee('Blocked');
    }

    public function test_technical_official_can_manage_coaches_separately_and_status_is_shared_with_team_page(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $team = $this->createTeam($official, 'KickHub United');
        $player = $this->createPlayer($team, 'Team Player');
        $coach = Jurulatih::create([
            'id_pasukan' => $team->id,
            'nama' => 'Team Coach',
            'umur' => 42,
        ]);
        $coachListUrl = route('technical-official.player-suspensions.teams.show', [
            'pasukan' => $team,
            'section' => 'coaches',
        ]);

        $this->actingAs($official)
            ->get(route('technical-official.player-suspensions.index', ['section' => 'coaches']))
            ->assertOk()
            ->assertSee('Coaches')
            ->assertSee('1 coach')
            ->assertSee($coachListUrl);

        $this->get($coachListUrl)
            ->assertOk()
            ->assertSee('Coach Status')
            ->assertSee('Team Coach')
            ->assertSee('Active')
            ->assertDontSee($player->nama)
            ->assertSee(route('technical-official.player-suspensions.coaches.toggle', $coach));

        $this->patch(route('technical-official.player-suspensions.coaches.toggle', $coach))
            ->assertRedirect($coachListUrl)
            ->assertSessionHas('status', 'Coach blocked successfully.');

        $this->assertDatabaseHas('jurulatih', [
            'id' => $coach->id,
            'is_blocked' => true,
        ]);
        $this->get(route('teams.show', $team))
            ->assertOk()
            ->assertSee('Team Coach')
            ->assertSee('Blocked');

        $this->patch(route('technical-official.player-suspensions.coaches.toggle', $coach))
            ->assertRedirect($coachListUrl)
            ->assertSessionHas('status', 'Coach activated successfully.');

        $this->assertDatabaseHas('jurulatih', [
            'id' => $coach->id,
            'is_blocked' => false,
        ]);
        $this->get(route('teams.show', $team))
            ->assertOk()
            ->assertSee('Team Coach')
            ->assertSee('Active');
    }

    public function test_two_yellow_cards_across_matches_show_an_automatic_one_match_suspension_until_served(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $manager = User::factory()->create();
        $team = $this->createTeam($manager, 'KickHub United');
        $opponents = collect(range(1, 4))
            ->map(fn (int $number): Pasukan => $this->createTeam($manager, "Opponent {$number}"));
        $player = $this->createPlayer($team, 'Test Player');
        $firstMatch = $this->createMatch($team, $opponents[0]);
        $firstMatch->update([
            'tarikh' => '2026-11-10',
            'skor_pasukan_1' => 1,
            'skor_pasukan_2' => 0,
        ]);
        $secondMatch = $this->createMatch($team, $opponents[1]);
        $secondMatch->update([
            'tarikh' => '2026-11-11',
            'skor_pasukan_1' => 1,
            'skor_pasukan_2' => 0,
        ]);
        $servingMatch = $this->createMatch($team, $opponents[2]);
        $servingMatch->update(['tarikh' => '2026-11-12']);
        $followingMatch = $this->createMatch($team, $opponents[3]);
        $followingMatch->update(['tarikh' => '2026-11-13']);

        foreach ([$firstMatch, $secondMatch] as $match) {
            RekodDisiplin::create([
                'id_perlawanan' => $match->id,
                'id_pemain' => $player->id,
                'jenis_kad' => 'kuning',
                'minit' => 20,
            ]);
        }

        $this->assertDatabaseHas('sekatan_pemain', [
            'id_pemain' => $player->id,
            'jenis_sekatan' => SekatanPemain::TYPE_DISCIPLINARY,
            'id_perlawanan_digantung' => $servingMatch->id,
            'status' => SekatanPemain::STATUS_PENDING,
        ]);

        $this->actingAs($official)
            ->get(route('technical-official.player-suspensions.teams.show', $team))
            ->assertOk()
            ->assertSee('One red card or two yellow cards automatically suspend a player for one match.')
            ->assertSee('Use manual blocking only for assaults after a match.')
            ->assertSee('Automatic suspension')
            ->assertSee('aria-label="Block Test Player"', false);

        $this->get(route('teams.show', $team))
            ->assertOk()
            ->assertSee('Automatic suspension');

        $servingMatch->update([
            'skor_pasukan_1' => 0,
            'skor_pasukan_2' => 0,
        ]);

        $this->assertDatabaseHas('sekatan_pemain', [
            'id_pemain' => $player->id,
            'jenis_sekatan' => SekatanPemain::TYPE_DISCIPLINARY,
            'id_perlawanan_digantung' => $servingMatch->id,
            'status' => SekatanPemain::STATUS_SERVED,
        ]);
        $this->assertTrue(app(PlayerSuspensionService::class)->isEligibleForMatch($player, $followingMatch));

        $this->get(route('teams.show', $team))
            ->assertOk()
            ->assertSee('Active')
            ->assertDontSee('Automatic suspension');
    }

    public function test_players_cannot_be_managed_by_unauthenticated_or_non_technical_users(): void
    {
        $team = $this->createTeam(User::factory()->create(), 'KickHub United');
        $player = $this->createPlayer($team, 'Test Player');
        $coach = Jurulatih::create([
            'id_pasukan' => $team->id,
            'nama' => 'Test Coach',
            'umur' => 40,
        ]);

        $this->get(route('technical-official.player-suspensions.index'))
            ->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create(['role' => User::ROLE_TEAM_MANAGER]))
            ->post(route('technical-official.player-suspensions.store', $player))
            ->assertForbidden();

        $this->get(route('technical-official.player-suspensions.teams.show', $team))
            ->assertForbidden();
        $this->patch(route('technical-official.player-suspensions.coaches.toggle', $coach))
            ->assertForbidden();

        $this->assertDatabaseMissing('sekatan_pemain', [
            'id_pemain' => $player->id,
            'jenis_sekatan' => SekatanPemain::TYPE_MANUAL,
        ]);
    }

    private function createTeam(User $manager, string $name): Pasukan
    {
        return Pasukan::create([
            'nama_pasukan' => $name,
            'nama_kolej' => 'Melaka',
            'id_pengurus' => $manager->id,
        ]);
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

    private function createMatch(Pasukan $team, Pasukan $opponent): Perlawanan
    {
        return Perlawanan::create([
            'id_pasukan_1' => $team->id,
            'id_pasukan_2' => $opponent->id,
            'tarikh' => '2026-11-20',
            'masa' => '10:00:00',
            'tempat' => 'Stadium',
        ]);
    }
}
