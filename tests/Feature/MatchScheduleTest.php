<?php

namespace Tests\Feature;

use App\Models\JaringanGol;
use App\Models\Kumpulan;
use App\Models\Pasukan;
use App\Models\Pemain;
use App\Models\Perlawanan;
use App\Models\RekodDisiplin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchScheduleTest extends TestCase
{
    use RefreshDatabase;

    public function test_schedule_displays_real_fixtures_and_filters_matches_by_day(): void
    {
        $manager = User::factory()->create();
        $groupA = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $groupB = Kumpulan::create(['nama_kumpulan' => 'Kumpulan B']);
        $alpha = $this->createTeam('KV Alpha', $groupA, $manager, 'images/alpha.png');
        $bravo = $this->createTeam('KV Bravo', $groupA, $manager);
        $charlie = $this->createTeam('KV Charlie', $groupB, $manager);
        $delta = $this->createTeam('KV Delta', $groupB, $manager);

        $completedMatch = $this->createMatch($groupA, $alpha, $bravo, '2026-11-13', 2, 1);
        $upcomingMatch = $this->createMatch($groupB, $charlie, $delta, '2026-11-14');

        $response = $this->get(route('schedule'))->assertOk();

        $response->assertSee('Schedule')
            ->assertDontSee('KickHub tournament centre')
            ->assertDontSee('Scheduled matches')
            ->assertSee('KV Alpha')
            ->assertSee('KV Bravo')
            ->assertSee('KV Charlie')
            ->assertSee('Group A')
            ->assertSee('Group B')
            ->assertSee('Completed')
            ->assertSee('Upcoming')
            ->assertSee('2 - 1')
            ->assertSee('VS')
            ->assertSee('Test Stadium')
            ->assertSee(asset('images/alpha.png'))
            ->assertSee(route('matches.show', $completedMatch))
            ->assertSee(route('matches.show', $upcomingMatch))
            ->assertSee(route('statistics'))
            ->assertSee('data-match-day="1"', false)
            ->assertSee('data-match-day="2"', false)
            ->assertSee('bg-blue-900 text-white shadow-sm', false)
            ->assertSee('hover:bg-gray-50', false)
            ->assertSee('activeDay', false)
            ->assertSee('aria-pressed', false)
            ->assertSee('aria-label="KV Bravo initials"', false)
            ->assertSee('BR');
    }

    public function test_match_details_show_recorded_scores_scorers_and_events_in_chronological_order(): void
    {
        $manager = User::factory()->create();
        $group = Kumpulan::create(['nama_kumpulan' => 'Kumpulan A']);
        $alpha = $this->createTeam('KV Alpha', $group, $manager);
        $bravo = $this->createTeam('KV Bravo', $group, $manager);
        $topScorer = $this->createPlayer($alpha, 'Top Scorer', 1);
        $otherScorer = $this->createPlayer($bravo, 'Other Scorer', 2);
        $match = $this->createMatch($group, $alpha, $bravo, '2026-11-13', 2, 1);

        foreach ([$topScorer, $topScorer, $otherScorer] as $player) {
            JaringanGol::create([
                'id_perlawanan' => $match->id,
                'id_pemain' => $player->id,
            ]);
        }
        RekodDisiplin::create([
            'id_perlawanan' => $match->id,
            'id_pemain' => $topScorer->id,
            'jenis_kad' => 'merah',
            'minit' => 70,
        ]);
        RekodDisiplin::create([
            'id_perlawanan' => $match->id,
            'id_pemain' => $otherScorer->id,
            'jenis_kad' => 'kuning',
            'minit' => 15,
        ]);

        $response = $this->get(route('matches.show', $match))->assertOk();

        $response->assertSee('Match Details')
            ->assertSee('Back to Schedule')
            ->assertSee(route('schedule'))
            ->assertSee('2 - 1')
            ->assertSee('Completed')
            ->assertSee('Group A')
            ->assertSee('Top Scorer')
            ->assertSee('Other Scorer')
            ->assertSee('2 goals')
            ->assertSee('1 goal')
            ->assertSee('Goal minute not recorded')
            ->assertSee('Yellow card')
            ->assertSee('Red card')
            ->assertSeeInOrder([
                'data-match-minute="15"',
                'data-match-minute="70"',
                'data-match-minute="unknown"',
            ], false);
    }

    public function test_upcoming_match_details_show_no_fabricated_score_or_events(): void
    {
        $manager = User::factory()->create();
        $group = Kumpulan::create(['nama_kumpulan' => 'Kumpulan B']);
        $alpha = $this->createTeam('KV Alpha', $group, $manager);
        $bravo = $this->createTeam('KV Bravo', $group, $manager);
        $match = $this->createMatch($group, $alpha, $bravo, '2026-11-14');

        $this->get(route('matches.show', $match))
            ->assertOk()
            ->assertSee('VS')
            ->assertSee('Upcoming')
            ->assertSee('No goals have been recorded for this match.')
            ->assertSee('No match events have been recorded.')
            ->assertDontSee('0 - 0');
    }

    public function test_missing_match_details_return_not_found(): void
    {
        $this->get(route('matches.show', 999999))->assertNotFound();
    }

    private function createTeam(string $name, Kumpulan $group, User $manager, ?string $logo = null): Pasukan
    {
        return Pasukan::create([
            'nama_pasukan' => $name,
            'nama_kolej' => 'Melaka',
            'id_pengurus' => $manager->id,
            'id_kumpulan' => $group->id,
            'logo' => $logo,
        ]);
    }

    private function createPlayer(Pasukan $team, string $name, int $number): Pemain
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
        string $date,
        ?int $score1 = null,
        ?int $score2 = null,
    ): Perlawanan {
        return Perlawanan::create([
            'id_kumpulan' => $group->id,
            'id_pasukan_1' => $team1->id,
            'id_pasukan_2' => $team2->id,
            'tarikh' => $date,
            'masa' => '10:00:00',
            'tempat' => 'Test Stadium',
            'skor_pasukan_1' => $score1,
            'skor_pasukan_2' => $score2,
        ]);
    }
}
