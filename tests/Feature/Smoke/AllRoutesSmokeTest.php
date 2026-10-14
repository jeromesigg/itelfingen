<?php

namespace Tests\Feature\Smoke;

use App\Models\BexioToken;
use App\Models\User;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route as Router;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Ruft jede GET-Route der App mit jeder Rolle auf und erwartet keinen Serverfehler.
 * Neue Routen werden automatisch mitgetestet; fehlt ein Wert für einen Routenparameter,
 * schlägt der Test fehl und routeParameters() muss ergänzt werden.
 *
 * Berechtigungen prüft Security/RoleAccessTest, hier geht es nur um Abstürze.
 */
class AllRoutesSmokeTest extends TestCase
{
    /**
     * Routen, die hier bewusst nicht aufgerufen werden, jeweils mit Grund.
     */
    private const EXCLUDED = [
        '_ignition/health-check' => 'Debug-Werkzeug von Ignition',
        'api/user' => 'Laravel-Beispielroute ohne api-Guard',
    ];

    /**
     * Bekannte, noch nicht behobene Serverfehler. Der Test bleibt grün, solange genau diese auftreten.
     * Ist einer behoben, schlägt der Test fehl, bis der Eintrag hier entfernt ist.
     */
    private const KNOWN_ISSUES = [
        // 'route/{uri}' => ['roles' => ['rolle'], 'reason' => 'Warum es (noch) kracht'],
    ];

    /**
     * anonym = nicht eingeloggt, registriert = selbst registriert ohne Rolle.
     */
    public static function roles(): array
    {
        return [
            'anonym' => ['anonym'],
            'registriert' => ['registered'],
            'gast' => ['gast'],
            'team' => ['team'],
            'verwalter' => ['verwalter'],
            'admin' => ['admin'],
        ];
    }

    #[DataProvider('roles')]
    public function test_no_get_route_returns_a_server_error(string $role): void
    {
        Notification::fake();
        // Bexio verbunden, aber ohne echte Aufrufe (z. B. Artikel-Abgleich unter admin/positions/create).
        BexioToken::create(['access_token' => 'token', 'refresh_token' => 'refresh', 'expires_at' => now()->addHour()]);
        Http::fake(['api.bexio.com/*' => Http::response([])]);
        $scenario = BookingScenario::create();
        $user = $role === 'anonym' ? null : $scenario->user($role);
        $errors = [];

        foreach ($this->getRoutes() as $route) {
            foreach ($this->buildUris($route, $scenario) as $uri) {
                $error = $this->visit($uri, $user);
                if ($error) {
                    $errors[$route->uri()][] = "$uri → $error";
                }
            }
        }

        $known = array_keys(array_filter(self::KNOWN_ISSUES, fn (array $issue) => in_array($role, $issue['roles'])));
        $unexpected = array_merge([], ...array_values(array_diff_key($errors, array_flip($known))));
        $fixed = array_values(array_diff($known, array_keys($errors)));

        $this->assertSame([], $unexpected, "Neue Serverfehler als $role:\n".implode("\n", $unexpected));
        $this->assertSame([], $fixed, "Behoben als $role? Bitte aus KNOWN_ISSUES entfernen:\n".implode("\n", $fixed));
    }

    /**
     * @return string|null Fehlerbeschreibung bei Status >= 500
     */
    private function visit(string $uri, ?User $user): ?string
    {
        // Frische Instanz wie bei einem echten Request, sonst hängen geladene Relationen vom vorherigen Request am User.
        if ($user) {
            $this->actingAs($user->fresh());
        }
        $response = $this->get($uri);

        // Jeder Request startet ohne Login und Session des vorherigen.
        $this->app['auth']->forgetGuards();
        $this->flushSession();

        if ($response->getStatusCode() < 500) {
            return null;
        }
        $exception = $response->exception;

        return $response->getStatusCode().($exception
            ? ' '.$exception->getMessage().' ('.basename($exception->getFile()).':'.$exception->getLine().')'
            : '');
    }

    /**
     * @return Route[]
     */
    private function getRoutes(): array
    {
        return collect(Router::getRoutes()->getRoutes())
            ->filter(fn (Route $route) => in_array('GET', $route->methods()))
            // Routen von Paketen (Laravel, Livewire) gehören nicht zur App
            ->reject(fn (Route $route) => str_starts_with($route->getActionName(), 'Laravel\\')
                || str_starts_with($route->getActionName(), 'Livewire\\')
                || str_starts_with($route->uri(), 'livewire')
                || str_starts_with($route->uri(), 'storage/')
                || $route->uri() === 'up'
                || array_key_exists($route->uri(), self::EXCLUDED))
            ->values()
            ->all();
    }

    /**
     * Jede Route mit den Standardwerten und zusätzlich mit Randfall-Objekten aus dem Szenario.
     *
     * @return string[]
     */
    private function buildUris(Route $route, BookingScenario $s): array
    {
        $values = $this->routeParameters($s);
        $edgeCases = [
            'faq' => $s->faqWithoutPhoto->id,
            'person' => $s->personWithoutPhoto->id,
            // Stornierte Buchung ohne Positionen und ohne Checkliste
            'event' => $s->cancelledEvent->id,
            'uuid' => $s->cancelledEvent->uuid,
            // Selbst registriert: ohne Rolle
            'user' => $s->registered->id,
        ] + $values;

        return array_values(array_unique([
            $this->buildUri($route, $values),
            $this->buildUri($route, $edgeCases),
        ]));
    }

    private function buildUri(Route $route, array $values): string
    {
        return '/'.ltrim(preg_replace_callback('/\{(\w+)\??\}/', function ($match) use ($values, $route) {
            $this->assertArrayHasKey(
                $match[1],
                $values,
                "Kein Testwert für Parameter {{$match[1]}} der Route {$route->uri()}. Bitte in routeParameters() ergänzen."
            );

            return $values[$match[1]];
        }, $route->uri()), '/');
    }

    /**
     * Werte für Routenparameter, so wie die App sie in Links erzeugt.
     */
    private function routeParameters(BookingScenario $s): array
    {
        return [
            'api_token' => 'test-api-token',
            'days' => 3,
            'number' => '0791234567',
            'token' => 'invalid-token',
            'uuid' => $s->confirmedEvent->uuid,
            'event' => $s->confirmedEvent->id,
            'contact' => $s->contact->id,
            'application' => $s->application->id,
            'room' => $s->room->id,
            'checkpoint' => $s->checkpoint->id,
            'user' => $s->verwalter->id,
            'homepage' => 1,
            'faq' => $s->faq->id,
            'faq_chapter' => $s->faqChapter->id,
            'history' => $s->history->id,
            'testimonial' => $s->testimonial->id,
            'person' => $s->person->id,
            'picture' => $s->picture->id,
            'position' => $s->pricelistPosition->id,
            'newsletter' => $s->newsletter->id,
        ];
    }
}
