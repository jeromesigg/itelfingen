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

    public function test_logged_in_user_is_redirected_away_from_login(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('team'))->get('/login')->assertRedirect('/');
    }

    public function test_self_registration_is_disabled(): void
    {
        // Konten legt nur ein Admin an (Benutzerverwaltung).
        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'username' => 'eindringling',
            'password' => 'passwort123',
            'password_confirmation' => 'passwort123',
        ])->assertNotFound();

        $this->assertDatabaseMissing('users', ['username' => 'eindringling']);
    }

    public function test_password_reset_and_confirm_are_disabled(): void
    {
        // Benutzer haben keine E-Mail-Adresse, ein Reset per Mail kann nicht funktionieren.
        foreach (['/password/reset', '/password/reset/token', '/password/confirm'] as $uri) {
            $this->get($uri)->assertNotFound();
        }
        $this->post('/password/email', ['email' => 'x@example.com'])->assertNotFound();
    }

    public function test_login_page_renders(): void
    {
        $this->get('/login')->assertOk();
    }
}
