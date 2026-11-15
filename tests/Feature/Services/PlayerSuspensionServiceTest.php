<?php

namespace Tests\Feature\Services;

use App\Models\Pasukan;
use App\Models\Pemain;
use App\Models\Perlawanan;
use App\Models\RekodDisiplin;
use App\Models\SekatanPemain;
use App\Models\User;
use App\Services\PlayerSuspensionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerSuspensionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_pair_of_yellow_cards_suspends_the_player_for_the_next_match_until_served(): void
    {
        $manager = User::factory()->create();
        $team = $this->createTeam('KickHub United', $manager);
        $opponents = collect(range(1, 6))
            ->map(fn (int $number): Pasukan => $this->createTeam("Opponent {$number}", $manager));
        $player = $this->createPlayer($team);
        $matches = collect(range(0, 5))
            ->map(fn (int $index): Perlawanan => $this->createMatch(
                $team,
                $opponents[$index],
                sprintf('2026-11-%02d', 10 + $index),
                $index === 2 || $index === 5 ? null : 1,
                $index === 2 || $index === 5 ? null : 0,
            ));

        $this->createCard($matches[0], $player, 'kuning');
        $secondYellow = $this->createCard($matches[1], $player, 'kuning');

        $service = app(PlayerSuspensionService::class);

        $this->assertDatabaseHas('sekatan_pemain', [
            'id_pemain' => $player->id,
            'jenis_sekatan' => SekatanPemain::TYPE_DISCIPLINARY,
            'id_rekod_disiplin' => $secondYellow->id,
            'id_perlawanan_digantung' => $matches[2]->id,
            'status' => SekatanPemain::STATUS_PENDING,
        ]);
        $this->assertFalse($service->isEligibleForMatch($player, $matches[2]));
        $this->assertFalse($service->isEligibleForMatch($player, $matches[3]));

        $matches[2]->update([
            'skor_pasukan_1' => 0,
            'skor_pasukan_2' => 0,
        ]);

        $this->assertDatabaseHas('sekatan_pemain', [
            'id_rekod_disiplin' => $secondYellow->id,
            'status' => SekatanPemain::STATUS_SERVED,
        ]);
        $this->assertTrue($service->isEligibleForMatch($player, $matches[3]));

        $this->createCard($matches[3], $player, 'kuning');
        $fourthYellow = $this->createCard($matches[4], $player, 'kuning');

        $this->assertDatabaseHas('sekatan_pemain', [
            'id_rekod_disiplin' => $fourthYellow->id,
            'id_perlawanan_digantung' => $matches[5]->id,
            'status' => SekatanPemain::STATUS_PENDING,
        ]);
        $this->assertFalse($service->isEligibleForMatch($player, $matches[5]));
        $this->assertSame(2, $player->suspensions()->count());
    }

    public function test_red_card_suspension_is_recalculated_when_the_card_is_edited_or_deleted(): void
    {
        $manager = User::factory()->create();
        $team = $this->createTeam('KickHub United', $manager);
        $opponent = $this->createTeam('Opponent', $manager);
        $nextOpponent = $this->createTeam('Next Opponent', $manager);
        $followingOpponent = $this->createTeam('Following Opponent', $manager);
        $player = $this->createPlayer($team);
        $incidentMatch = $this->createMatch($team, $opponent, '2026-11-10', 1, 0);
        $suspendedMatch = $this->createMatch($team, $nextOpponent, '2026-11-11', null, null);
        $followingMatch = $this->createMatch($team, $followingOpponent, '2026-11-12', null, null);
        $redCard = $this->createCard($incidentMatch, $player, 'merah');
        $service = app(PlayerSuspensionService::class);

        $this->assertFalse($service->isEligibleForMatch($player, $suspendedMatch));
        $this->assertSame(1, $player->suspensions()->count());

        $redCard->update(['jenis_kad' => 'kuning']);

        $this->assertTrue($service->isEligibleForMatch($player, $suspendedMatch));
        $this->assertDatabaseMissing('sekatan_pemain', [
            'id_rekod_disiplin' => $redCard->id,
            'jenis_sekatan' => SekatanPemain::TYPE_DISCIPLINARY,
        ]);

        $redCard->update(['jenis_kad' => 'merah']);
        $this->assertFalse($service->isEligibleForMatch($player, $suspendedMatch));

        $suspendedMatch->update([
            'skor_pasukan_1' => 0,
            'skor_pasukan_2' => 0,
        ]);
        $this->assertTrue($service->isEligibleForMatch($player, $followingMatch));

        $redCard->delete();

        $this->assertTrue($service->isEligibleForMatch($player, $suspendedMatch));
        $this->assertSame(0, $player->suspensions()->count());
    }

    private function createTeam(string $name, User $manager): Pasukan
    {
        return Pasukan::create([
            'nama_pasukan' => $name,
            'nama_kolej' => 'Melaka',
            'id_pengurus' => $manager->id,
        ]);
    }

    private function createPlayer(Pasukan $team): Pemain
    {
        static $number = 0;
        $number++;

        return Pemain::create([
            'id_pasukan' => $team->id,
            'nama' => 'Test Player',
            'no_ic' => sprintf('%012d', $number),
            'no_jersi' => $number,
            'umur' => 20,
            'position' => 'Forward',
        ]);
    }

    private function createMatch(
        Pasukan $team,
        Pasukan $opponent,
        string $date,
        ?int $score1,
        ?int $score2,
    ): Perlawanan {
        return Perlawanan::create([
            'id_pasukan_1' => $team->id,
            'id_pasukan_2' => $opponent->id,
            'tarikh' => $date,
            'masa' => '10:00:00',
            'tempat' => 'Stadium',
            'skor_pasukan_1' => $score1,
            'skor_pasukan_2' => $score2,
        ]);
    }

    private function createCard(Perlawanan $match, Pemain $player, string $type): RekodDisiplin
    {
        return RekodDisiplin::create([
            'id_perlawanan' => $match->id,
            'id_pemain' => $player->id,
            'jenis_kad' => $type,
            'minit' => 20,
        ]);
    }
}
