<?php

namespace Tests\Feature;

use App\Models\Perlawanan;
use App\Models\User;
use Database\Seeders\MatchDayTwoSeeder;
use Database\Seeders\StatisticsDemoSeeder;
use Database\Seeders\TeamSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchDayTwoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_adds_four_upcoming_group_fixtures_without_duplicates(): void
    {
        User::factory()->create([
            'email' => 'user@gmail.com',
        ]);
        $this->seed(TeamSeeder::class);
        $this->seed(StatisticsDemoSeeder::class);

        $this->seed(MatchDayTwoSeeder::class);
        $this->seed(MatchDayTwoSeeder::class);

        $dayTwoMatches = Perlawanan::query()
            ->whereDate('tarikh', '2026-11-14')
            ->orderBy('masa')
            ->with(['pasukan1', 'pasukan2'])
            ->get();

        $this->assertDatabaseCount('perlawanan', 12);
        $this->assertSame(4, $dayTwoMatches->count());
        $this->assertSame(0, $dayTwoMatches->whereNotNull('skor_pasukan_1')->count());
        $this->assertSame(0, $dayTwoMatches->whereNotNull('skor_pasukan_2')->count());

        $this->assertSame([
            ['KV Datuk Seri Abu Zahar Isnin', 'KV Melaka Tengah'],
            ['KV Kuala Klawang', 'KV Port Dickson'],
            ['KV Ampangan', 'KV Juaseh'],
            ['KV Datuk Seri Mohd Zain', 'KV Datuk Lela Maharaja'],
        ], $dayTwoMatches->map(fn (Perlawanan $match): array => [
            $match->pasukan1->nama_pasukan,
            $match->pasukan2->nama_pasukan,
        ])->all());
    }
}
