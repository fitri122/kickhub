<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'name' => 'Team Manager',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role' => User::ROLE_TEAM_MANAGER,
            ],
        );

        User::updateOrCreate(
            ['email' => 'user1@gmail.com'],
            [
                'name' => 'Technical Official',
                'password' => Hash::make('password!'),
                'email_verified_at' => now(),
                'role' => User::ROLE_TECHNICAL_OFFICIAL,
            ],
        );

        $this->call(TeamSeeder::class);
    }
}
