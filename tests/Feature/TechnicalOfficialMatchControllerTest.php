<?php

namespace Tests\Feature;

use App\Models\JaringanGol;
use App\Models\Kumpulan;
use App\Models\Pasukan;
use App\Models\Pemain;
use App\Models\Perlawanan;
use App\Models\RekodDisiplin;
use App\Models\SekatanPemain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechnicalOfficialMatchControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_technical_official_can_create_an_upcoming_match_from_one_group(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $manager = User::factory()->create();
        $group = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $home = $this->createTeam('Home FC', $group, $manager);
        $away = $this->createTeam('Away FC', $group, $manager);

        $this->actingAs($official)
            ->get(route('technical-official.matches.create'))
            ->assertOk()
            ->assertSee('Choose a group first')
            ->assertSee('Team 1')
            ->assertSee('Team 2')
            ->assertSee('optgroup x-bind:label="team.name"', false);

        $this->post(route('technical-official.matches.store'), [
            'id_kumpulan' => $group->id,
            'id_pasukan_1' => $home->id,
            'id_pasukan_2' => $away->id,
            'tarikh' => '2026-11-20',
            'masa' => '13:30',
            'tempat' => 'Main Stadium',
        ])->assertRedirect();

        $match = Perlawanan::query()->sole();

        $this->assertDatabaseHas('perlawanan', [
            'id' => $match->id,
            'id_kumpulan' => $group->id,
            'id_pasukan_1' => $home->id,
            'id_pasukan_2' => $away->id,
            'tarikh' => '2026-11-20 00:00:00',
            'masa' => '13:30:00',
            'tempat' => 'Main Stadium',
            'match_label' => null,
            'skor_pasukan_1' => null,
            'skor_pasukan_2' => null,
        ]);

        $this->get(route('schedule'))
            ->assertOk()
            ->assertSee('Match #'.$match->id);
    }

    public function test_technical_official_can_set_and_clear_an_optional_match_label(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $manager = User::factory()->create();
        $group = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $home = $this->createTeam('Home FC', $group, $manager);
        $away = $this->createTeam('Away FC', $group, $manager);

        $this->actingAs($official)
            ->get(route('technical-official.matches.create'))
            ->assertOk()
            ->assertSee('Match Name / Stage (Optional)')
            ->assertSee('e.g. Semi Final or Final');

        $this->post(route('technical-official.matches.store'), [
            ...$this->matchInput($group, $home, $away),
            'match_label' => 'Semi Final',
        ])->assertRedirect();

        $match = Perlawanan::query()->sole();

        $this->assertSame('Semi Final', $match->match_label);
        $this->get(route('technical-official.matches.index'))
            ->assertOk()
            ->assertSee('Semi Final');
        $this->get(route('schedule'))
            ->assertOk()
            ->assertSee('Semi Final')
            ->assertDontSee('Match #'.$match->id);
        $this->get(route('matches.show', $match))
            ->assertOk()
            ->assertSee('Semi Final')
            ->assertDontSee('Match #'.$match->id);

        $this->put(route('technical-official.matches.update', $match), [
            ...$this->matchInput($group, $home, $away),
            'match_label' => '',
        ])->assertRedirect(route('technical-official.matches.edit', $match));

        $this->assertNull($match->refresh()->match_label);
        $this->get(route('schedule'))
            ->assertOk()
            ->assertSee('Match #'.$match->id);
    }

    public function test_match_label_cannot_exceed_255_characters(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $manager = User::factory()->create();
        $group = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $home = $this->createTeam('Home FC', $group, $manager);
        $away = $this->createTeam('Away FC', $group, $manager);

        $this->actingAs($official)
            ->from(route('technical-official.matches.create'))
            ->post(route('technical-official.matches.store'), [
                ...$this->matchInput($group, $home, $away),
                'match_label' => str_repeat('a', 256),
            ])
            ->assertSessionHasErrors('match_label');

        $this->assertDatabaseCount('perlawanan', 0);
    }

    public function test_match_creation_rejects_teams_from_different_groups(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $manager = User::factory()->create();
        $groupA = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $groupB = Kumpulan::create(['nama_kumpulan' => 'Kumpulan B']);
        $home = $this->createTeam('Home FC', $groupA, $manager);
        $away = $this->createTeam('Away FC', $groupB, $manager);

        $this->actingAs($official)
            ->from(route('technical-official.matches.create'))
            ->post(route('technical-official.matches.store'), $this->matchInput($groupA, $home, $away))
            ->assertSessionHasErrors('id_pasukan_2');

        $this->assertDatabaseCount('perlawanan', 0);
    }

    public function test_completed_match_saves_goal_scorers_cards_and_derived_score(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $manager = User::factory()->create();
        $group = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $home = $this->createTeam('Home FC', $group, $manager);
        $away = $this->createTeam('Away FC', $group, $manager);
        $homeScorer = $this->createPlayer($home, 'Home Scorer', 1);
        $awayScorer = $this->createPlayer($away, 'Away Scorer', 2);

        $this->actingAs($official)
            ->post(route('technical-official.matches.store'), [
                ...$this->matchInput($group, $home, $away),
                'is_completed' => '1',
                'goal_scorers' => [$homeScorer->id, $homeScorer->id, $awayScorer->id],
                'cards' => [[
                    'player_id' => $awayScorer->id,
                    'jenis_kad' => 'merah',
                    'minit' => 72,
                    'catatan' => 'Serious foul play',
                ]],
            ])
            ->assertRedirect();

        $match = Perlawanan::query()->sole();

        $this->assertSame(2, $match->skor_pasukan_1);
        $this->assertSame(1, $match->skor_pasukan_2);
        $this->assertSame(3, $match->jaringanGol()->count());
        $this->assertDatabaseHas('rekod_disiplin', [
            'id_perlawanan' => $match->id,
            'id_pemain' => $awayScorer->id,
            'jenis_kad' => 'merah',
            'minit' => 72,
            'catatan' => 'Serious foul play',
        ]);

        $this->get(route('schedule'))
            ->assertOk()
            ->assertSee('2 - 1')
            ->assertSee('Main Stadium');
        $this->get(route('matches.show', $match))
            ->assertOk()
            ->assertSee('Home Scorer')
            ->assertSee('Away Scorer')
            ->assertSee('Red card');
    }

    public function test_match_update_replaces_fixture_details_and_recorded_events(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $manager = User::factory()->create();
        $group = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $home = $this->createTeam('Home FC', $group, $manager);
        $away = $this->createTeam('Away FC', $group, $manager);
        $oldScorer = $this->createPlayer($home, 'Old Scorer', 1);
        $newScorer = $this->createPlayer($away, 'New Scorer', 2);
        $oldCardPlayer = $this->createPlayer($home, 'Old Card Player', 3);
        $newCardPlayer = $this->createPlayer($away, 'New Card Player', 4);
        $match = $this->createMatch($group, $home, $away);
        $oldGoal = JaringanGol::create([
            'id_perlawanan' => $match->id,
            'id_pemain' => $oldScorer->id,
        ]);
        $oldCard = RekodDisiplin::create([
            'id_perlawanan' => $match->id,
            'id_pemain' => $oldCardPlayer->id,
            'jenis_kad' => 'kuning',
            'minit' => 20,
        ]);

        $this->actingAs($official)
            ->put(route('technical-official.matches.update', $match), [
                ...$this->matchInput($group, $home, $away),
                'tarikh' => '2026-11-21',
                'masa' => '16:45',
                'tempat' => 'New Stadium',
                'is_completed' => '1',
                'goal_scorers' => [$newScorer->id],
                'cards' => [[
                    'player_id' => $newCardPlayer->id,
                    'jenis_kad' => 'kuning',
                    'minit' => 55,
                    'catatan' => '',
                ]],
            ])
            ->assertRedirect(route('technical-official.matches.edit', $match));

        $match->refresh();

        $this->assertSame('2026-11-21', $match->tarikh->toDateString());
        $this->assertSame('16:45:00', $match->masa);
        $this->assertSame('New Stadium', $match->tempat);
        $this->assertSame(0, $match->skor_pasukan_1);
        $this->assertSame(1, $match->skor_pasukan_2);
        $this->assertDatabaseMissing('jaringan_gol', ['id' => $oldGoal->id]);
        $this->assertDatabaseMissing('rekod_disiplin', ['id' => $oldCard->id]);
        $this->assertDatabaseHas('jaringan_gol', [
            'id_perlawanan' => $match->id,
            'id_pemain' => $newScorer->id,
        ]);
        $this->assertDatabaseHas('rekod_disiplin', [
            'id_perlawanan' => $match->id,
            'id_pemain' => $newCardPlayer->id,
            'jenis_kad' => 'kuning',
            'minit' => 55,
        ]);

        $this->put(route('technical-official.matches.update', $match), [
            ...$this->matchInput($group, $home, $away),
            'is_completed' => '0',
        ])->assertRedirect(route('technical-official.matches.edit', $match));

        $match->refresh();

        $this->assertNull($match->skor_pasukan_1);
        $this->assertNull($match->skor_pasukan_2);
        $this->assertDatabaseCount('jaringan_gol', 0);
        $this->assertDatabaseCount('rekod_disiplin', 0);
    }

    public function test_match_form_rejects_a_goal_scorer_from_a_non_participating_team(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $manager = User::factory()->create();
        $group = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $home = $this->createTeam('Home FC', $group, $manager);
        $away = $this->createTeam('Away FC', $group, $manager);
        $otherTeam = $this->createTeam('Other FC', $group, $manager);
        $otherPlayer = $this->createPlayer($otherTeam, 'Other Player', 1);

        $this->actingAs($official)
            ->from(route('technical-official.matches.create'))
            ->post(route('technical-official.matches.store'), [
                ...$this->matchInput($group, $home, $away),
                'is_completed' => '1',
                'goal_scorers' => [$otherPlayer->id],
            ])
            ->assertSessionHasErrors('goal_scorers.0');

        $this->assertDatabaseCount('perlawanan', 0);
    }

    public function test_technical_official_can_delete_one_match_and_its_related_events(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $manager = User::factory()->create();
        $group = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $home = $this->createTeam('Home FC', $group, $manager);
        $away = $this->createTeam('Away FC', $group, $manager);
        $scorer = $this->createPlayer($home, 'Home Scorer', 1);
        $cardedPlayer = $this->createPlayer($away, 'Carded Player', 2);
        $matchToDelete = $this->createMatch($group, $home, $away);
        $matchToKeep = $this->createMatch($group, $away, $home);
        $goal = JaringanGol::create([
            'id_perlawanan' => $matchToDelete->id,
            'id_pemain' => $scorer->id,
        ]);
        $card = RekodDisiplin::create([
            'id_perlawanan' => $matchToDelete->id,
            'id_pemain' => $cardedPlayer->id,
            'jenis_kad' => 'merah',
            'minit' => 80,
        ]);

        $this->actingAs($official)
            ->get(route('technical-official.matches.index'))
            ->assertOk()
            ->assertSee('Delete All Matches')
            ->assertSeeInOrder(['Match #1', 'Match #2'])
            ->assertSee('action="'.route('technical-official.matches.destroy', $matchToDelete).'"', false)
            ->assertSee('Delete Home FC vs Away FC and its recorded goals and cards?', false);

        $this->delete(route('technical-official.matches.destroy', $matchToDelete))
            ->assertRedirect(route('technical-official.matches.index'))
            ->assertSessionHas('status', 'Match and its recorded goals and cards deleted successfully.');

        $this->assertModelMissing($matchToDelete);
        $this->assertModelExists($matchToKeep);
        $this->assertDatabaseMissing('jaringan_gol', ['id' => $goal->id]);
        $this->assertDatabaseMissing('rekod_disiplin', ['id' => $card->id]);
        $this->assertDatabaseMissing('sekatan_pemain', [
            'id_pemain' => $cardedPlayer->id,
            'jenis_sekatan' => SekatanPemain::TYPE_DISCIPLINARY,
        ]);
        $this->assertDatabaseCount('pasukan', 2);
        $this->assertDatabaseCount('pemain', 2);
    }

    public function test_technical_official_can_delete_all_matches_and_related_events_without_deleting_teams_or_players(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $manager = User::factory()->create();
        $group = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $home = $this->createTeam('Home FC', $group, $manager);
        $away = $this->createTeam('Away FC', $group, $manager);
        $homePlayer = $this->createPlayer($home, 'Home Player', 1);
        $awayPlayer = $this->createPlayer($away, 'Away Player', 2);
        $matches = [
            $this->createMatch($group, $home, $away),
            $this->createMatch($group, $away, $home),
        ];

        foreach ($matches as $match) {
            JaringanGol::create([
                'id_perlawanan' => $match->id,
                'id_pemain' => $homePlayer->id,
            ]);
            RekodDisiplin::create([
                'id_perlawanan' => $match->id,
                'id_pemain' => $awayPlayer->id,
                'jenis_kad' => 'kuning',
                'minit' => 20,
            ]);
        }

        $this->actingAs($official)
            ->delete(route('technical-official.matches.destroy-all'))
            ->assertRedirect(route('technical-official.matches.index'))
            ->assertSessionHas('status', 'All matches and their recorded goals and cards deleted successfully.');

        $this->assertDatabaseCount('perlawanan', 0);
        $this->assertDatabaseCount('jaringan_gol', 0);
        $this->assertDatabaseCount('rekod_disiplin', 0);
        $this->assertDatabaseCount('sekatan_pemain', 0);
        $this->assertDatabaseCount('kumpulan', 1);
        $this->assertDatabaseCount('pasukan', 2);
        $this->assertDatabaseCount('pemain', 2);
        $this->assertDatabaseCount('users', 2);
    }

    public function test_only_technical_officials_can_manage_matches(): void
    {
        $this->get(route('technical-official.matches.index'))
            ->assertRedirect(route('login'));

        $manager = User::factory()->create(['role' => User::ROLE_TEAM_MANAGER]);
        $group = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $home = $this->createTeam('Home FC', $group, $manager);
        $away = $this->createTeam('Away FC', $group, $manager);
        $match = $this->createMatch($group, $home, $away);

        $this->actingAs($manager)
            ->get(route('technical-official.matches.create'))
            ->assertForbidden();

        $this->delete(route('technical-official.matches.destroy', $match))
            ->assertForbidden();
        $this->delete(route('technical-official.matches.destroy-all'))
            ->assertForbidden();

        $this->assertModelExists($match);
    }

    private function matchInput(Kumpulan $group, Pasukan $home, Pasukan $away): array
    {
        return [
            'id_kumpulan' => $group->id,
            'id_pasukan_1' => $home->id,
            'id_pasukan_2' => $away->id,
            'tarikh' => '2026-11-20',
            'masa' => '13:30',
            'tempat' => 'Main Stadium',
        ];
    }

    private function createTeam(string $name, Kumpulan $group, User $manager): Pasukan
    {
        return Pasukan::create([
            'nama_pasukan' => $name,
            'nama_kolej' => 'Melaka',
            'id_pengurus' => $manager->id,
            'id_kumpulan' => $group->id,
        ]);
    }

    private function createPlayer(Pasukan $team, string $name, int $number): Pemain
    {
        return Pemain::create([
            'id_pasukan' => $team->id,
            'nama' => $name,
            'no_ic' => fake()->unique()->numerify('############'),
            'no_jersi' => $number,
            'umur' => 20,
            'position' => 'Forward',
        ]);
    }

    private function createMatch(Kumpulan $group, Pasukan $home, Pasukan $away): Perlawanan
    {
        return Perlawanan::create([
            ...$this->matchInput($group, $home, $away),
            'masa' => '10:00:00',
        ]);
    }
}
