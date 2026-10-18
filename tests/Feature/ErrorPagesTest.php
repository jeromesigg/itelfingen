<?php

namespace Tests\Feature;

use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Tests\TestCase;

/**
 * Eigene Fehlerseiten (Deutsch, mit Weg zurück) und abgelaufene Formulare (CSRF).
 */
class ErrorPagesTest extends TestCase
{
    public function test_not_found_page_is_german_with_link_home(): void
    {
        $this->get('/diese-seite-gibt-es-nicht')
            ->assertNotFound()
            ->assertSee('Seite nicht gefunden')
            ->assertSee('Zur Startseite')
            ->assertDontSee('Not Found');
    }

    public function test_expired_booking_form_returns_with_input_and_message(): void
    {
        $session = $this->app['session.store'];
        $session->setPreviousUrl(url('/'));
        $request = Request::create('/event/create', 'POST', ['_token' => 'abgelaufen', '_form_scope' => 'event', 'name' => 'Muster']);
        $request->setLaravelSession($session);

        $response = $this->app->make(ExceptionHandler::class)->render($request, new TokenMismatchException);

        $this->assertSame(url('/').'#booking', $response->getTargetUrl());
        $this->assertSame('Muster', $session->getOldInput('name'));
        $this->assertNull($session->getOldInput('_token'));
        $this->assertStringContainsString('zu lange offen', $session->get('errors')->getBag('event')->first());
    }

    public function test_expired_form_without_scope_uses_default_error_bag(): void
    {
        $session = $this->app['session.store'];
        $session->setPreviousUrl(url('/applications'));
        $request = Request::create('/applications/store', 'POST', ['_token' => 'abgelaufen', 'name' => 'Muster']);
        $request->setLaravelSession($session);

        $response = $this->app->make(ExceptionHandler::class)->render($request, new TokenMismatchException);

        $this->assertSame(url('/applications'), $response->getTargetUrl());
        $this->assertTrue($session->get('errors')->getBag('default')->has('expired'));
    }
}
