<?php

namespace Tests\Feature;

use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Login, Logout und Registrierung.
 */
class AuthTest extends TestCase
{
    public function test_admin_logs_in_with_username(): void
    {
        $this->post('/login', ['username' => 'Administrator', 'password' => 'password'])
            ->assertRedirect();

        $this->assertAuthenticated();
    }

    public function test_wrong_password_is_rejected(): void
    {
        $this->post('/login', ['username' => 'Administrator', 'password' => 'falsch']);

        $this->assertGuest();
    }

    public function test_logged_in_user_is_redirected_away_from_login_and_register(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('team'))->get('/login')->assertRedirect('/');
        $this->actingAs($s->user('team'))->get('/register')->assertRedirect('/');
    }

    public function test_registered_user_waits_for_activation(): void
    {
        $this->post('/register', [
            'username' => 'neu_registriert',
            'password' => 'passwort123',
            'password_confirmation' => 'passwort123',
        ])->assertRedirect('/login');

        $this->assertGuest();
        $this->assertDatabaseHas('users', ['username' => 'neu_registriert', 'role_id' => null, 'is_active' => false]);
    }
}
