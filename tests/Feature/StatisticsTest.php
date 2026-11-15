<?php

namespace Tests\Feature;

use App\Models\JaringanGol;
use App\Models\Pasukan;
use App\Models\Pemain;
use App\Models\Perlawanan;
use App\Models\RekodDisiplin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatisticsTest extends TestCase
{
    use RefreshDatabase;

    public function test_statistics_page_shows_every_scorer_and_every_teams_clean_sheet_total(): void
    {
        $data = $this->createStatisticsData();

        $response = $this->get(route('statistics'))->assertOk();

        $response->assertViewHas('totalMatches', 6)
            ->assertViewHas('totalGoals', 13)
            ->assertViewHas('totalScorers', 10)
            ->assertViewHas('yellowCardTotal', 3)
            ->assertViewHas('redCardTotal', 3)
            ->assertViewHas('topScorers', fn ($scorers): bool => $scorers->map(fn ($scorer): array => [
                $scorer->player_name,
                $scorer->team_name,
                (int) $scorer->score,
                $scorer->player_image,
            ])->all() === [
                ['Top Scorer', 'KV Alpha', 3, 'images/top-scorer.png'],
                ['Second Scorer', 'KV Bravo', 2, null],
                ['Eighth Scorer', 'KV Bravo', 1, null],
                ['Fifth Scorer', 'KV Bravo', 1, null],
                ['Fourth Scorer', 'KV Alpha', 1, null],
                ['Ninth Scorer', 'KV Charlie', 1, null],
                ['Seventh Scorer', 'KV Bravo', 1, null],
                ['Sixth Scorer', 'KV Charlie', 1, null],
                ['Tenth Scorer', 'KV Charlie', 1, null],
                ['Third Scorer', 'KV Charlie', 1, null],
            ])
            ->assertViewHas('cleanSheets', fn ($teams): bool => $teams->map(fn ($team): array => [
                $team->nama_pasukan,
                (int) $team->clean_sheets,
            ])->all() === [
                ['KV Bravo', 2],
                ['KV Alpha', 1],
                ['KV Charlie', 1],
                ['KV Delta', 0],
            ]);

        $response->assertSee(route('statistics.yellow-cards'))
            ->assertSee(route('statistics.red-cards'))
            ->assertSee('Top Scorer')
            ->assertSee('Clean Sheet')
            ->assertSee('Total Goals')
            ->assertSee('10 players scored')
            ->assertSee($data['topScorer']->nama)
            ->assertSee($data['topScorer']->pasukan->nama_pasukan)
            ->assertSee(asset('images/top-scorer.png'))
            ->assertSee(asset('images/alpha.png'))
            ->assertSee('Third Scorer');
        $this->assertSame(10, $response->viewData('topScorers')->count());
    }

    public function test_statistics_recalculate_goal_and_clean_sheet_totals_after_records_change(): void
    {
        $data = $this->createStatisticsData();

        $data['topScorer']->jaringanGol()->firstOrFail()->delete();
        $data['alphaWin']->update(['skor_pasukan_2' => 1]);

        $this->get(route('statistics'))
            ->assertOk()
            ->assertViewHas('totalGoals', 12)
            ->assertViewHas('totalScorers', 10)
            ->assertViewHas('topScorers', fn ($scorers): bool => (int) $scorers->firstWhere('player_name', 'Top Scorer')->score === 2)
            ->assertViewHas('cleanSheets', fn ($teams): bool => (int) $teams->firstWhere('nama_pasukan', 'KV Alpha')->clean_sheets === 0);

        $data['alphaWin']->delete();

        $this->get(route('statistics'))
            ->assertOk()
            ->assertViewHas('totalGoals', 9)
            ->assertViewHas('totalScorers', 8)
            ->assertViewHas('topScorers', fn ($scorers): bool => $scorers->doesntContain(
                fn ($scorer): bool => $scorer->player_name === 'Top Scorer',
            ))
            ->assertViewHas('cleanSheets', fn ($teams): bool => (int) $teams->firstWhere('nama_pasukan', 'KV Alpha')->clean_sheets === 0);
    }

    public function test_card_pages_group_each_player_and_sort_by_card_count(): void
    {
        $data = $this->createStatisticsData();

        $yellowResponse = $this->get(route('statistics.yellow-cards'))->assertOk();
        $yellowResponse->assertViewHas('totalCards', 3)
            ->assertViewHas('cardPlayers', fn ($players): bool => $players->map(fn ($player): array => [
                $player->player_name,
                (int) $player->card_count,
            ])->all() === [
                ['Top Scorer', 2],
                ['Second Scorer', 1],
            ])
            ->assertSee('YELLOW CARD')
            ->assertSee('Back to Statistics')
            ->assertSeeInOrder(['Top Scorer', 'KV Alpha', 'Yellow Card', '2', 'Second Scorer', 'KV Bravo', 'Yellow Card', '1']);

        $this->assertSame(1, substr_count($yellowResponse->getContent(), 'data-player-id="'.$data['topScorer']->id.'"'));
        $this->assertSame(1, substr_count($yellowResponse->getContent(), 'data-player-id="'.$data['secondScorer']->id.'"'));

        $redResponse = $this->get(route('statistics.red-cards'))->assertOk();
        $redResponse->assertViewHas('totalCards', 3)
            ->assertViewHas('cardPlayers', fn ($players): bool => $players->map(fn ($player): array => [
                $player->player_name,
                (int) $player->card_count,
            ])->all() === [
                ['Second Scorer', 2],
                ['Third Scorer', 1],
            ])
            ->assertSee('RED CARD')
            ->assertSee('Back to Statistics')
            ->assertSeeInOrder(['Second Scorer', 'KV Bravo', 'Red Card', '2', 'Third Scorer', 'KV Charlie', 'Red Card', '1']);

        $this->assertSame(1, substr_count($redResponse->getContent(), 'data-player-id="'.$data['secondScorer']->id.'"'));
        $this->assertSame(1, substr_count($redResponse->getContent(), 'data-player-id="'.$data['thirdScorer']->id.'"'));
    }

    private function createStatisticsData(): array
    {
        $manager = User::factory()->create();
        $alpha = $this->createTeam('KV Alpha', $manager, 'images/alpha.png');
        $bravo = $this->createTeam('KV Bravo', $manager);
        $charlie = $this->createTeam('KV Charlie', $manager);
        $delta = $this->createTeam('KV Delta', $manager);

        $topScorer = $this->createPlayer('Top Scorer', $alpha, 1, 'images/top-scorer.png');
        $secondScorer = $this->createPlayer('Second Scorer', $bravo, 2);
        $thirdScorer = $this->createPlayer('Third Scorer', $charlie, 3);
        $fourthScorer = $this->createPlayer('Fourth Scorer', $alpha, 4);
        $fifthScorer = $this->createPlayer('Fifth Scorer', $bravo, 5);
        $sixthScorer = $this->createPlayer('Sixth Scorer', $charlie, 6);
        $seventhScorer = $this->createPlayer('Seventh Scorer', $bravo, 7);
        $eighthScorer = $this->createPlayer('Eighth Scorer', $bravo, 8);
        $ninthScorer = $this->createPlayer('Ninth Scorer', $charlie, 9);
        $tenthScorer = $this->createPlayer('Tenth Scorer', $charlie, 10);

        $alphaWin = $this->createMatch($alpha, $bravo, 4, 0);
        $scorelessDraw = $this->createMatch($alpha, $charlie, 0, 0);
        $bravoWin = $this->createMatch($bravo, $delta, 4, 0);
        $secondBravoWin = $this->createMatch($bravo, $charlie, 1, 0);
        $charlieWin = $this->createMatch($charlie, $delta, 4, 0);
        $this->createMatch($alpha, $delta, null, null);

        $this->createGoals($alphaWin, $topScorer, 3);
        $this->createGoals($alphaWin, $fourthScorer, 1);
        $this->createGoals($bravoWin, $secondScorer, 1);
        $this->createGoals($bravoWin, $fifthScorer, 1);
        $this->createGoals($bravoWin, $seventhScorer, 1);
        $this->createGoals($bravoWin, $eighthScorer, 1);
        $this->createGoals($secondBravoWin, $secondScorer, 1);
        $this->createGoals($charlieWin, $thirdScorer, 1);
        $this->createGoals($charlieWin, $sixthScorer, 1);
        $this->createGoals($charlieWin, $ninthScorer, 1);
        $this->createGoals($charlieWin, $tenthScorer, 1);

        $this->createCard($alphaWin, $topScorer, 'kuning', 15);
        $this->createCard($scorelessDraw, $topScorer, 'kuning', 30);
        $this->createCard($bravoWin, $secondScorer, 'kuning', 40);
        $this->createCard($bravoWin, $secondScorer, 'merah', 60);
        $this->createCard($secondBravoWin, $secondScorer, 'merah', 70);
        $this->createCard($charlieWin, $thirdScorer, 'merah', 80);

        return compact('topScorer', 'secondScorer', 'thirdScorer', 'alphaWin');
    }

    private function createTeam(string $name, User $manager, ?string $logo = null): Pasukan
    {
        return Pasukan::create([
            'nama_pasukan' => $name,
            'nama_kolej' => 'Melaka',
            'id_pengurus' => $manager->id,
            'logo' => $logo,
        ]);
    }

    private function createPlayer(string $name, Pasukan $team, int $number, ?string $image = null): Pemain
    {
        return Pemain::create([
            'id_pasukan' => $team->id,
            'nama' => $name,
            'no_ic' => sprintf('%012d', $number),
            'no_jersi' => $number,
            'umur' => 20,
            'gambar' => $image,
            'position' => 'Forward',
        ]);
    }

    private function createMatch(Pasukan $team1, Pasukan $team2, ?int $score1, ?int $score2): Perlawanan
    {
        return Perlawanan::create([
            'id_pasukan_1' => $team1->id,
            'id_pasukan_2' => $team2->id,
            'tarikh' => '2026-11-12',
            'masa' => '10:00:00',
            'tempat' => 'Stadium',
            'skor_pasukan_1' => $score1,
            'skor_pasukan_2' => $score2,
        ]);
    }

    private function createGoals(Perlawanan $match, Pemain $player, int $count): void
    {
        foreach (range(1, $count) as $_) {
            JaringanGol::create([
                'id_perlawanan' => $match->id,
                'id_pemain' => $player->id,
            ]);
        }
    }

    private function createCard(Perlawanan $match, Pemain $player, string $cardType, int $minute): void
    {
        RekodDisiplin::create([
            'id_perlawanan' => $match->id,
            'id_pemain' => $player->id,
            'jenis_kad' => $cardType,
            'minit' => $minute,
        ]);
    }
}
