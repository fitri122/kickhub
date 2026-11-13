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

    public function test_statistics_page_shows_database_totals_and_top_three_rankings(): void
    {
        $data = $this->createStatisticsData();

        $response = $this->get(route('statistics'))->assertOk();

        $response->assertViewHas('totalMatches', 6)
            ->assertViewHas('totalScorers', 3)
            ->assertViewHas('yellowCardTotal', 3)
            ->assertViewHas('redCardTotal', 3)
            ->assertViewHas('topScorers', fn ($scorers): bool => $scorers->map(fn ($scorer): array => [
                $scorer->player_name,
                $scorer->team_name,
                (int) $scorer->score,
            ])->all() === [
                ['Top Scorer', 'KV Alpha', 3],
                ['Second Scorer', 'KV Bravo', 2],
                ['Third Scorer', 'KV Charlie', 1],
            ])
            ->assertViewHas('cleanSheets', [
                ['team_name' => 'KV Alpha', 'clean_sheets' => 2],
                ['team_name' => 'KV Bravo', 'clean_sheets' => 2],
                ['team_name' => 'KV Charlie', 'clean_sheets' => 2],
            ]);

        $response->assertSee(route('statistics.yellow-cards'))
            ->assertSee(route('statistics.red-cards'))
            ->assertSee('Top Scorer')
            ->assertSee('Clean Sheet')
            ->assertSee($data['topScorer']->nama)
            ->assertSee($data['topScorer']->pasukan->nama_pasukan);
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
        $alpha = $this->createTeam('KV Alpha', $manager);
        $bravo = $this->createTeam('KV Bravo', $manager);
        $charlie = $this->createTeam('KV Charlie', $manager);
        $delta = $this->createTeam('KV Delta', $manager);

        $topScorer = $this->createPlayer('Top Scorer', $alpha, 1);
        $secondScorer = $this->createPlayer('Second Scorer', $bravo, 2);
        $thirdScorer = $this->createPlayer('Third Scorer', $charlie, 3);

        $alphaWin = $this->createMatch($alpha, $bravo, 3, 0);
        $scorelessDraw = $this->createMatch($alpha, $charlie, 0, 0);
        $bravoWin = $this->createMatch($bravo, $delta, 1, 0);
        $secondBravoWin = $this->createMatch($bravo, $charlie, 1, 0);
        $charlieWin = $this->createMatch($charlie, $delta, 1, 0);
        $this->createMatch($alpha, $delta, null, null);

        $this->createGoals($alphaWin, $topScorer, 3);
        $this->createGoals($bravoWin, $secondScorer, 1);
        $this->createGoals($secondBravoWin, $secondScorer, 1);
        $this->createGoals($charlieWin, $thirdScorer, 1);

        $this->createCard($alphaWin, $topScorer, 'kuning', 15);
        $this->createCard($scorelessDraw, $topScorer, 'kuning', 30);
        $this->createCard($bravoWin, $secondScorer, 'kuning', 40);
        $this->createCard($bravoWin, $secondScorer, 'merah', 60);
        $this->createCard($secondBravoWin, $secondScorer, 'merah', 70);
        $this->createCard($charlieWin, $thirdScorer, 'merah', 80);

        return compact('topScorer', 'secondScorer', 'thirdScorer');
    }

    private function createTeam(string $name, User $manager): Pasukan
    {
        return Pasukan::create([
            'nama_pasukan' => $name,
            'nama_kolej' => 'Melaka',
            'id_pengurus' => $manager->id,
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
