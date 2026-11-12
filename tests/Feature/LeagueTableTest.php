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

    private function createTeam(string $name, Kumpulan $group, User $manager): Pasukan
    {
        return Pasukan::create([
            'nama_pasukan' => $name,
            'nama_kolej' => 'Melaka',
            'id_pengurus' => $manager->id,
            'id_kumpulan' => $group->id,
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
