<?php

namespace Tests\Feature;

use App\Models\Kumpulan;
use App\Models\Pasukan;
use App\Models\Pemain;
use App\Models\Perlawanan;
use App\Models\RekodDisiplin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeagueTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_standings_apply_win_draw_loss_points_and_recalculate_after_a_result_edit(): void
    {
        $manager = User::factory()->create();
        $group = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $alpha = $this->createTeam('Alpha FC', $group, $manager);
        $bravo = $this->createTeam('Bravo FC', $group, $manager);
        $charlie = $this->createTeam('Charlie FC', $group, $manager);

        $this->createMatch($group, $alpha, $bravo, 2, 0);
        $this->createMatch($group, $alpha, $charlie, 1, 1);
        $bravoCharlieMatch = $this->createMatch($group, $bravo, $charlie, 1, 0);

        $this->get(route('league-table'))
            ->assertOk()
            ->assertViewHas('tables', fn (array $tables): bool => array_map(
                fn (array $standing): array => [
                    $standing['team_name'],
                    $standing['played'],
                    $standing['wins'],
                    $standing['draws'],
                    $standing['losses'],
                    $standing['goals_for'],
                    $standing['goals_against'],
                    $standing['goal_difference'],
                    $standing['points'],
                ],
                $tables[0]['standings'],
            ) === [
                ['Alpha FC', 2, 1, 1, 0, 3, 1, 2, 4],
                ['Bravo FC', 2, 1, 0, 1, 1, 2, -1, 3],
                ['Charlie FC', 2, 0, 1, 1, 1, 2, -1, 1],
            ]);

        $bravoCharlieMatch->update([
            'skor_pasukan_1' => 0,
            'skor_pasukan_2' => 1,
        ]);

        $this->get(route('league-table'))
            ->assertOk()
            ->assertViewHas('tables', fn (array $tables): bool => array_column($tables[0]['standings'], 'team_name') === [
                'Alpha FC',
                'Charlie FC',
                'Bravo FC',
            ]);

        $bravoCharlieMatch->delete();

        $this->get(route('league-table'))
            ->assertOk()
            ->assertViewHas('tables', fn (array $tables): bool => array_map(
                fn (array $standing): array => [$standing['team_name'], $standing['played'], $standing['points']],
                $tables[0]['standings'],
            ) === [
                ['Alpha FC', 2, 4],
                ['Charlie FC', 1, 1],
                ['Bravo FC', 1, 0],
            ]);
    }

    public function test_team_with_fewer_cards_ranks_higher_when_points_and_goal_stats_are_equal(): void
    {
        $manager = User::factory()->create();
        $group = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $ampangan = $this->createTeam('KV Ampangan', $group, $manager);
        $juaseh = $this->createTeam('KV Juaseh', $group, $manager);
        $ampanganPlayer = $this->createPlayer('Ampangan Player', $ampangan, 1);
        $juasehPlayer = $this->createPlayer('Juaseh Player', $juaseh, 2);
        $match = $this->createMatch($group, $ampangan, $juaseh, 0, 0);

        foreach ([$ampanganPlayer, $ampanganPlayer, $juasehPlayer] as $player) {
            RekodDisiplin::create([
                'id_perlawanan' => $match->id,
                'id_pemain' => $player->id,
                'jenis_kad' => 'kuning',
                'minit' => 20,
            ]);
        }

        $this->get(route('league-table'))
            ->assertOk()
            ->assertViewHas('tables', fn (array $tables): bool => array_map(
                fn (array $standing): array => [
                    $standing['team_name'],
                    $standing['points'],
                    $standing['goal_difference'],
                    $standing['goals_for'],
                    $standing['yellow_cards'],
                ],
                $tables[0]['standings'],
            ) === [
                ['KV Juaseh', 1, 0, 0, 1],
                ['KV Ampangan', 1, 0, 0, 2],
            ]);
    }

    public function test_league_table_combines_groups_and_ranks_completed_matches_with_team_statistics(): void
    {
        $manager = User::factory()->create();
        $groupA = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $groupB = Kumpulan::create(['nama_kumpulan' => 'Kumpulan B']);

        $alpha = $this->createTeam('Alpha FC', $groupA, $manager);
        $bravo = $this->createTeam('Bravo FC', $groupA, $manager);
        $charlie = $this->createTeam('Charlie FC', $groupB, $manager);
        $delta = $this->createTeam('Delta FC', $groupB, $manager);

        $alphaPlayer = $this->createPlayer('Alpha Player', $alpha, 1);
        $deltaPlayer = $this->createPlayer('Delta Player', $delta, 2);

        $alphaMatch = $this->createMatch($groupA, $alpha, $bravo, 2, 0);
        $charlieMatch = $this->createMatch($groupB, $charlie, $delta, 1, 0);
        $this->createMatch($groupA, $alpha, $bravo, null, null);

        RekodDisiplin::create([
            'id_perlawanan' => $alphaMatch->id,
            'id_pemain' => $alphaPlayer->id,
            'jenis_kad' => 'kuning',
            'minit' => 20,
        ]);
        RekodDisiplin::create([
            'id_perlawanan' => $charlieMatch->id,
            'id_pemain' => $deltaPlayer->id,
            'jenis_kad' => 'merah',
            'minit' => 75,
        ]);

        $overallResponse = $this->get(route('league-table'))->assertOk();
        $groupsResponse = $this->get(route('league-standings'))->assertOk();

        $overallResponse->assertSee('Overall League Standings')
            ->assertSee(route('league-standings'))
            ->assertSee('Group A')
            ->assertSee('Group B')
            ->assertSee('GP (Group)')
            ->assertSee('Goals For')
            ->assertSee('Goals Against')
            ->assertSee('Goal Difference')
            ->assertSee('Yellow Card')
            ->assertSee('Red Card')
            ->assertSee('PTS');
        $overallResponse->assertSeeInOrder(['GP (Group)', 'P', 'W', 'D', 'L', 'GF', 'GA', 'GD', 'YC', 'RC', 'PTS']);
        $overallResponse->assertSeeInOrder(['Alpha FC', 'Charlie FC', 'Delta FC', 'Bravo FC']);
        $overallResponse->assertSeeInOrder([
            'Alpha FC',
            'Group A',
            '1',
            '1',
            '0',
            '0',
            '2',
            '0',
            '2',
            '1',
            '0',
            '3',
        ]);
        $overallResponse->assertSeeInOrder([
            'Delta FC',
            'Group B',
            '1',
            '0',
            '0',
            '1',
            '0',
            '1',
            '-1',
            '0',
            '1',
            '0',
        ]);

        $overallHtml = $overallResponse->getContent();
        $groupsHtml = $groupsResponse->getContent();
        $this->assertSame(1, substr_count($overallHtml, 'GP (Group)'));
        $this->assertSame(0, substr_count($groupsHtml, 'GP (Group)'));
        $groupsResponse->assertSee('Group A')
            ->assertSee('Group B')
            ->assertSee(route('league-table'))
            ->assertDontSee('id="overall-standings"', false);

        foreach (['Alpha FC', 'Bravo FC', 'Charlie FC', 'Delta FC'] as $teamName) {
            $this->assertSame(1, substr_count($overallHtml, $teamName));
            $this->assertSame(1, substr_count($groupsHtml, $teamName));
        }
    }

    public function test_technical_official_can_save_group_selection_and_move_teams_between_groups(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $manager = User::factory()->create();
        $groupA = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $groupB = Kumpulan::create(['nama_kumpulan' => 'Kumpulan B']);
        $alpha = $this->createTeam('Alpha FC', $groupA, $manager);
        $bravo = $this->createTeam('Bravo FC', $groupA, $manager);
        $charlie = $this->createTeam('Charlie FC', $groupB, $manager);
        $delta = $this->createTeam('Delta FC', null, $manager);
        $echo = $this->createTeam('Echo FC', null, $manager);
        $foxtrot = $this->createTeam('Foxtrot FC', null, $manager);

        $this->actingAs($official)
            ->get(route('league-standings'))
            ->assertOk()
            ->assertSee('Manage Team Groups')
            ->assertSee('Group A')
            ->assertSee('Group B')
            ->assertSee(route('technical-official.league-standings.groups.teams.update', $groupA), false)
            ->assertSee('name="team_ids[]"', false);

        $this->patch(route('technical-official.league-standings.groups.teams.update', $groupA), [
            'team_ids' => [$alpha->id, $charlie->id, $delta->id, $echo->id],
        ])
            ->assertRedirect(route('league-standings'))
            ->assertSessionHas('status', 'Teams updated for Group A.');

        $this->assertDatabaseHas('pasukan', [
            'id' => $alpha->id,
            'id_kumpulan' => $groupA->id,
        ]);
        $this->assertDatabaseHas('pasukan', [
            'id' => $charlie->id,
            'id_kumpulan' => $groupA->id,
        ]);
        $this->assertDatabaseHas('pasukan', [
            'id' => $bravo->id,
            'id_kumpulan' => null,
        ]);
        $this->assertDatabaseHas('pasukan', [
            'id' => $foxtrot->id,
            'id_kumpulan' => null,
        ]);
        $this->get(route('league-standings'))
            ->assertOk()
            ->assertSee('Alpha FC')
            ->assertSee('Charlie FC');

        $this->patch(route('technical-official.league-standings.groups.teams.update', $groupB), [
            'team_ids' => [$bravo->id],
        ])->assertRedirect(route('league-standings'));

        $this->assertDatabaseHas('pasukan', [
            'id' => $bravo->id,
            'id_kumpulan' => $groupB->id,
        ]);
        $this->assertDatabaseHas('pasukan', [
            'id' => $charlie->id,
            'id_kumpulan' => $groupA->id,
        ]);
    }

    public function test_only_technical_officials_can_change_team_groups(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_TEAM_MANAGER]);
        $group = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $team = $this->createTeam('Alpha FC', null, $manager);
        $route = route('technical-official.league-standings.groups.teams.update', $group);

        $this->get(route('league-standings'))
            ->assertOk()
            ->assertDontSee('Manage Team Groups');

        $this->patch($route, [
            'team_ids' => [$team->id],
        ])->assertRedirect(route('login'));

        $this->actingAs($manager)
            ->get(route('league-standings'))
            ->assertOk()
            ->assertDontSee('Manage Team Groups');

        $this->patch($route, [
            'team_ids' => [$team->id],
        ])->assertForbidden();

        $this->assertNull($team->refresh()->id_kumpulan);
    }

    public function test_group_assignment_is_capped_at_four_distinct_existing_teams(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $manager = User::factory()->create();
        $group = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $teams = collect(range(1, 5))
            ->map(fn (int $number): Pasukan => $this->createTeam("Team {$number}", null, $manager));

        $this->actingAs($official)
            ->from(route('league-standings'))
            ->patch(route('technical-official.league-standings.groups.teams.update', $group), [
                'team_ids' => $teams->pluck('id')->all(),
            ])
            ->assertSessionHasErrors('team_ids');

        $this->assertSame(0, Pasukan::query()->where('id_kumpulan', $group->id)->count());

        $this->patch(route('technical-official.league-standings.groups.teams.update', $group), [
            'team_ids' => [99999],
        ])->assertSessionHasErrors('team_ids.0');

        $this->patch(route('technical-official.league-standings.groups.teams.update', $group), [
            'team_ids' => [$teams[0]->id, $teams[0]->id],
        ])->assertSessionHasErrors('team_ids.1');

        $this->assertSame(0, Pasukan::query()->where('id_kumpulan', $group->id)->count());
    }

    public function test_saving_an_empty_group_selection_unassigns_current_members(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $manager = User::factory()->create();
        $group = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $teamA = $this->createTeam('Alpha FC', $group, $manager);
        $teamB = $this->createTeam('Bravo FC', $group, $manager);

        $this->actingAs($official)
            ->patch(route('technical-official.league-standings.groups.teams.update', $group), [])
            ->assertRedirect(route('league-standings'));

        $this->assertNull($teamA->refresh()->id_kumpulan);
        $this->assertNull($teamB->refresh()->id_kumpulan);
    }

    private function createTeam(string $name, ?Kumpulan $group, User $manager): Pasukan
    {
        return Pasukan::create([
            'nama_pasukan' => $name,
            'nama_kolej' => 'Melaka',
            'id_pengurus' => $manager->id,
            'id_kumpulan' => $group?->id,
        ]);
    }

    private function createPlayer(string $name, Pasukan $team, int $number): Pemain
    {
        return Pemain::create([
            'id_pasukan' => $team->id,
            'nama' => $name,
            'no_ic' => sprintf('%012d', $number),
            'no_jersi' => $number,
            'umur' => 20,
            'position' => 'Forward',
        ]);
    }

    private function createMatch(
        Kumpulan $group,
        Pasukan $team1,
        Pasukan $team2,
        ?int $score1,
        ?int $score2,
    ): Perlawanan {
        return Perlawanan::create([
            'id_kumpulan' => $group->id,
            'id_pasukan_1' => $team1->id,
            'id_pasukan_2' => $team2->id,
            'tarikh' => '2026-11-12',
            'masa' => '10:00:00',
            'tempat' => 'Stadium',
            'skor_pasukan_1' => $score1,
            'skor_pasukan_2' => $score2,
        ]);
    }
}
