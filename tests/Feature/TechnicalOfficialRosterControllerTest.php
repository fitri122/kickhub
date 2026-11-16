<?php

namespace Tests\Feature;

use App\Models\Pasukan;
use App\Models\Pemain;
use App\Models\PencapaianPemain;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer as XLSXWriter;
use Tests\TestCase;

class TechnicalOfficialRosterControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_technical_official_dashboard_links_to_team_rosters(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);

        $this->actingAs($official)
            ->get(route('technical-official.dashboard'))
            ->assertOk()
            ->assertSee('Team Rosters')
            ->assertSee('href="'.route('technical-official.rosters.index').'"', false);
    }

    public function test_technical_official_can_add_a_player_and_coach_manually(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $team = $this->createTeam($official, 'KickHub United');

        $this->actingAs($official)
            ->post(route('technical-official.rosters.players.store', $team), [
                'player_name' => 'New Player',
                'player_national_id' => '123456789012',
                'player_jersey_number' => 12,
                'player_age' => 21,
                'player_role' => 'Captain',
                'player_position' => 'Midfielder',
            ])
            ->assertRedirect(route('technical-official.rosters.teams.show', $team))
            ->assertSessionHas('status', 'Player added successfully.');

        $this->assertDatabaseHas('pemain', [
            'id_pasukan' => $team->id,
            'nama' => 'New Player',
            'no_ic' => '123456789012',
            'no_jersi' => 12,
            'role_pemain' => 'Captain',
            'position' => 'Midfielder',
        ]);

        $this->post(route('technical-official.rosters.coaches.store', $team), [
            'coach_name' => 'New Coach',
            'coach_age' => 42,
        ])
            ->assertRedirect(route('technical-official.rosters.teams.show', $team))
            ->assertSessionHas('status', 'Coach added successfully.');

        $this->assertDatabaseHas('jurulatih', [
            'id_pasukan' => $team->id,
            'nama' => 'New Coach',
            'umur' => 42,
        ]);
    }

    public function test_technical_official_can_update_player_role_position_and_achievements(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $team = $this->createTeam($official, 'KickHub United');
        $player = $this->createPlayer($team, 'Test Player');
        $oldAchievement = PencapaianPemain::create([
            'id_pemain' => $player->id,
            'nama_pencapaian' => 'Old Award',
            'tahun' => 2020,
            'peringkat' => 'District',
        ]);

        $this->actingAs($official)
            ->get(route('technical-official.rosters.players.edit', [$team, $player]))
            ->assertOk()
            ->assertSee('Old Award')
            ->assertSee('Achievements');

        $this->put(route('technical-official.rosters.players.update', [$team, $player]), [
            'name' => 'Updated Player',
            'national_id' => $player->no_ic,
            'jersey_number' => 8,
            'age' => 22,
            'role' => 'Vice Captain',
            'position' => 'Goalkeeper',
            'achievements' => [
                ['name' => 'Cup Winner', 'year' => 2025, 'level' => 'National'],
                ['name' => '', 'year' => '', 'level' => ''],
            ],
        ])
            ->assertRedirect(route('technical-official.rosters.players.edit', [$team, $player]))
            ->assertSessionHas('status', 'Player details updated successfully.');

        $this->assertDatabaseHas('pemain', [
            'id' => $player->id,
            'nama' => 'Updated Player',
            'no_jersi' => 8,
            'role_pemain' => 'Vice Captain',
            'position' => 'Goalkeeper',
        ]);
        $this->assertModelMissing($oldAchievement);
        $this->assertDatabaseHas('pencapaian_pemain', [
            'id_pemain' => $player->id,
            'nama_pencapaian' => 'Cup Winner',
            'tahun' => 2025,
            'peringkat' => 'National',
        ]);

        $this->get(route('teams.show', $team))
            ->assertOk()
            ->assertSee('Goalkeeper')
            ->assertSee('Cup Winner')
            ->assertSee('National');
    }

    public function test_technical_official_can_import_player_and_coach_csv_files(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $team = $this->createTeam($official, 'KickHub United');
        $playerFile = UploadedFile::fake()->createWithContent(
            'players.csv',
            "name,national_id,jersey_number,age,role,position\nImported Player,123456789012,7,20,Player,Forward\n",
        );
        $coachFile = UploadedFile::fake()->createWithContent(
            'coaches.csv',
            "name,age\nImported Coach,45\n",
        );

        $this->actingAs($official)
            ->post(route('technical-official.rosters.players.import', $team), ['players_file' => $playerFile])
            ->assertRedirect(route('technical-official.rosters.teams.show', $team))
            ->assertSessionHas('status', '1 players imported successfully.');

        $this->assertDatabaseHas('pemain', [
            'id_pasukan' => $team->id,
            'nama' => 'Imported Player',
            'no_ic' => '123456789012',
            'position' => 'Forward',
        ]);

        $this->post(route('technical-official.rosters.coaches.import', $team), ['coaches_file' => $coachFile])
            ->assertRedirect(route('technical-official.rosters.teams.show', $team))
            ->assertSessionHas('status', '1 coaches imported successfully.');

        $this->assertDatabaseHas('jurulatih', [
            'id_pasukan' => $team->id,
            'nama' => 'Imported Coach',
            'umur' => 45,
        ]);
    }

    public function test_xlsx_player_import_is_supported(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $team = $this->createTeam($official, 'KickHub United');
        $temporaryPath = tempnam(sys_get_temp_dir(), 'roster-');
        $writer = new XLSXWriter;
        $writer->openToFile($temporaryPath);
        $writer->addRow(Row::fromValues(['name', 'national_id', 'jersey_number', 'age', 'role', 'position']));
        $writer->addRow(Row::fromValues(['Excel Player', '223456789012', '9', '19', 'Player', 'Defender']));
        $writer->close();
        $file = UploadedFile::fake()->createWithContent('players.xlsx', file_get_contents($temporaryPath));
        unlink($temporaryPath);

        $this->actingAs($official)
            ->post(route('technical-official.rosters.players.import', $team), ['players_file' => $file])
            ->assertRedirect(route('technical-official.rosters.teams.show', $team))
            ->assertSessionHas('status', '1 players imported successfully.');

        $this->assertDatabaseHas('pemain', [
            'id_pasukan' => $team->id,
            'nama' => 'Excel Player',
            'no_ic' => '223456789012',
            'position' => 'Defender',
        ]);
    }

    public function test_invalid_player_import_rejects_all_rows_without_partial_changes(): void
    {
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $team = $this->createTeam($official, 'KickHub United');
        $file = UploadedFile::fake()->createWithContent(
            'players.csv',
            "name,national_id,jersey_number,age,role,position\nValid Player,323456789012,7,20,Player,Forward\nInvalid Player,not-an-id,8,19,Player,Defender\n",
        );

        $this->actingAs($official)
            ->from(route('technical-official.rosters.teams.show', $team))
            ->post(route('technical-official.rosters.players.import', $team), ['players_file' => $file])
            ->assertSessionHasErrors('players_file');

        $this->assertDatabaseCount('pemain', 0);
    }

    public function test_only_technical_officials_can_manage_rosters_and_players_cannot_be_edited_through_another_team(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_TEAM_MANAGER]);
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $team = $this->createTeam($manager, 'Team One');
        $otherTeam = $this->createTeam($manager, 'Team Two');
        $player = $this->createPlayer($team, 'Team One Player');

        $this->get(route('technical-official.rosters.index'))
            ->assertRedirect(route('login'));

        $this->actingAs($manager)
            ->get(route('technical-official.rosters.index'))
            ->assertForbidden();

        $this->actingAs($official)
            ->get(route('technical-official.rosters.players.edit', [$otherTeam, $player]))
            ->assertNotFound();

        $this->assertModelExists($player);
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
        return $team->pemain()->create([
            'nama' => $name,
            'no_ic' => fake()->unique()->numerify('############'),
            'no_jersi' => fake()->numberBetween(1, 99),
            'umur' => 20,
            'role_pemain' => 'Player',
            'position' => 'Forward',
        ]);
    }
}
