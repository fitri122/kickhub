<?php

namespace Tests\Feature;

use App\Models\JaringanGol;
use App\Models\Pasukan;
use App\Models\Pemain;
use App\Models\Perlawanan;
use App\Models\RekodDisiplin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MatchEventIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_goal_record_rejects_a_player_from_a_team_not_in_the_match(): void
    {
        $manager = User::factory()->create();
        $home = $this->createTeam('Home FC', $manager);
        $away = $this->createTeam('Away FC', $manager);
        $other = $this->createTeam('Other FC', $manager);
        $player = $this->createPlayer($other);
        $match = $this->createMatch($home, $away);

        $this->expectException(ValidationException::class);

        JaringanGol::create([
            'id_perlawanan' => $match->id,
            'id_pemain' => $player->id,
        ]);
    }

    public function test_card_record_rejects_a_player_from_a_team_not_in_the_match(): void
    {
        $manager = User::factory()->create();
        $home = $this->createTeam('Home FC', $manager);
        $away = $this->createTeam('Away FC', $manager);
        $other = $this->createTeam('Other FC', $manager);
        $player = $this->createPlayer($other);
        $match = $this->createMatch($home, $away);

        $this->expectException(ValidationException::class);

        RekodDisiplin::create([
            'id_perlawanan' => $match->id,
            'id_pemain' => $player->id,
            'jenis_kad' => 'kuning',
            'minit' => 20,
        ]);
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
        return Pemain::create([
            'id_pasukan' => $team->id,
            'nama' => 'Test Player',
            'no_ic' => '123456789012',
            'no_jersi' => 1,
            'umur' => 20,
            'position' => 'Forward',
        ]);
    }

    private function createMatch(Pasukan $home, Pasukan $away): Perlawanan
    {
        return Perlawanan::create([
            'id_pasukan_1' => $home->id,
            'id_pasukan_2' => $away->id,
            'tarikh' => '2026-11-10',
            'masa' => '10:00:00',
            'tempat' => 'Stadium',
        ]);
    }
}
