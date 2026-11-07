<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_login_redirects_each_role_to_its_dashboard(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_TEAM_MANAGER]);
        $this->post('/login', ['email' => $manager->email, 'password' => 'password'])
            ->assertRedirect(route('team-manager.dashboard', absolute: false));

        $this->post('/logout');

        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $this->post('/login', ['email' => $official->email, 'password' => 'password'])
            ->assertRedirect(route('technical-official.dashboard', absolute: false));
    }

    public function test_role_dashboards_are_only_accessible_to_the_assigned_role(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_TEAM_MANAGER]);
        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $regularUser = User::factory()->create();

        $this->actingAs($manager)->get(route('team-manager.dashboard'))->assertOk();
        $this->actingAs($manager)->get(route('technical-official.dashboard'))->assertForbidden();
        $this->actingAs($official)->get(route('technical-official.dashboard'))->assertOk();
        $this->actingAs($official)->get(route('team-manager.dashboard'))->assertForbidden();
        $this->actingAs($regularUser)->get(route('team-manager.dashboard'))->assertForbidden();
        $this->actingAs($regularUser)->get(route('technical-official.dashboard'))->assertForbidden();
    }

    public function test_role_navigation_links_are_shown_only_for_the_matching_role(): void
    {
        $manager = User::factory()->create(['role' => User::ROLE_TEAM_MANAGER]);
        $this->actingAs($manager)->get('/')
            ->assertSee('Team Manager')
            ->assertDontSee('Technical Official');

        $official = User::factory()->create(['role' => User::ROLE_TECHNICAL_OFFICIAL]);
        $this->actingAs($official)->get('/')
            ->assertSee('Technical Official')
            ->assertDontSee('Team Manager');

        $regularUser = User::factory()->create();
        $this->actingAs($regularUser)->get('/')
            ->assertDontSee('Team Manager')
            ->assertDontSee('Technical Official');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
