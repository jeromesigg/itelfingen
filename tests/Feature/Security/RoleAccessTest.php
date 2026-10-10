<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Wer darf welche Route im Admin-Bereich aufrufen (abgestimmte Tabelle in tests/README.md).
 *
 * Stufen: team < verwalter < admin. Eingeloggte ohne Recht bekommen 403, Gäste werden zum Login umgeleitet.
 * Schreibende Routen werden nur auf "verboten" geprüft; erlaubte Schreibzugriffe testen die Fachtests.
 */
class RoleAccessTest extends TestCase
{
    private const LEVELS = ['team' => 1, 'verwalter' => 2, 'admin' => 3];

    /**
     * Platzhalter in den URIs: {event}, {contact}, {application}, {room}, {checkpoint}, {newsletter}, {position}, {self}, {other}.
     *
     * @return array<string, array{string, string, string}>
     */
    public static function pages(): array
    {
        return [
            // Team: Dashboard, Graphen, Buchungen ansehen, eigenes Profil
            'dashboard' => ['GET', '/admin', 'team'],
            'changes' => ['GET', '/admin/changes', 'team'],
            'charts' => ['GET', '/admin/bookings', 'team'],
            'events index' => ['GET', '/admin/events', 'team'],
            'events table' => ['GET', '/events/createDataTables?status=Alle&date=Alle', 'team'],
            'users index' => ['GET', '/admin/users', 'team'],
            'own profile' => ['GET', '/admin/users/{self}/edit', 'team'],

            // Verwalter: Buchungen bearbeiten, Anfragen, Bewerbungen, Checkliste, Newsletter, Exporte
            'events create' => ['GET', '/admin/events/create', 'verwalter'],
            'events edit' => ['GET', '/admin/events/{event}/edit', 'verwalter'],
            'bookings export' => ['GET', '/admin/bookings/export-csv', 'verwalter'],
            'contacts index' => ['GET', '/admin/contacts', 'verwalter'],
            'contacts table' => ['GET', '/contacts/createDataTables?done=Alle', 'verwalter'],
            'applications index' => ['GET', '/admin/applications', 'verwalter'],
            'applications table' => ['GET', '/applications/createDataTables', 'verwalter'],
            'applications show' => ['GET', '/admin/applications/{application}', 'verwalter'],
            'newsletter index' => ['GET', '/admin/newsletter', 'verwalter'],
            'newsletter table' => ['GET', '/newsletter/createDataTables', 'verwalter'],
            'newsletter export bookings' => ['GET', '/admin/newsletter/export-bookings', 'verwalter'],
            'newsletter export members' => ['GET', '/admin/newsletter/export-members', 'verwalter'],
            'rooms index' => ['GET', '/admin/rooms', 'verwalter'],
            'rooms edit' => ['GET', '/admin/rooms/{room}/edit', 'verwalter'],
            'checkpoints index' => ['GET', '/admin/checkpoints', 'verwalter'],
            'checkpoints edit' => ['GET', '/admin/checkpoints/{checkpoint}/edit', 'verwalter'],

            // Admin: Inhalte und Benutzerverwaltung
            'homepages index' => ['GET', '/admin/homepages', 'admin'],
            'homepage edit' => ['GET', '/admin/homepages/1/edit', 'admin'],
            'pictures index' => ['GET', '/admin/pictures', 'admin'],
            'testimonials index' => ['GET', '/admin/testimonials', 'admin'],
            'people index' => ['GET', '/admin/people', 'admin'],
            'histories index' => ['GET', '/admin/histories', 'admin'],
            'faqs index' => ['GET', '/admin/faqs', 'admin'],
            'faqs table' => ['GET', '/faqs/createDataTables', 'admin'],
            'faq chapters index' => ['GET', '/admin/faq_chapters', 'admin'],
            'pricelist index' => ['GET', '/admin/positions', 'admin'],
            'other user edit' => ['GET', '/admin/users/{other}/edit', 'admin'],
            'maintenance' => ['GET', '/admin/maintenance', 'admin'],
        ];
    }

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function actions(): array
    {
        return [
            'events store' => ['POST', '/admin/events', 'verwalter'],
            'events update' => ['PATCH', '/admin/events/{event}', 'verwalter'],
            'events destroy' => ['DELETE', '/admin/events/{event}', 'verwalter'],
            'events cleaning mail' => ['POST', '/admin/events/{event}/SendCleaningMail', 'verwalter'],
            'events parse' => ['POST', '/admin/events/parse', 'verwalter'],
            'events door code' => ['POST', '/admin/events/createAPICode', 'verwalter'],
            'booking comment' => ['PATCH', '/admin/homepages/1/comment_update', 'verwalter'],
            'booking mail text' => ['PATCH', '/admin/homepages/1/mail_text_update', 'verwalter'],
            'contact done' => ['POST', '/contacts/{contact}/done', 'verwalter'],
            'application refuse' => ['POST', '/applications/{application}/refuse', 'verwalter'],
            'rooms store' => ['POST', '/admin/rooms', 'verwalter'],
            'rooms destroy' => ['DELETE', '/admin/rooms/{room}', 'verwalter'],
            'checkpoints store' => ['POST', '/admin/checkpoints', 'verwalter'],
            'newsletter update' => ['PATCH', '/admin/newsletter/{newsletter}', 'verwalter'],
            'newsletter destroy' => ['DELETE', '/admin/newsletter/{newsletter}', 'verwalter'],

            'homepage update' => ['PATCH', '/admin/homepages/1', 'admin'],
            'pricelist update' => ['PATCH', '/admin/positions/{position}', 'admin'],
            'faqs store' => ['POST', '/admin/faqs', 'admin'],
            'users store' => ['POST', '/admin/users', 'admin'],
            'other user update' => ['PATCH', '/admin/users/{other}', 'admin'],
            'other user destroy' => ['DELETE', '/admin/users/{other}', 'admin'],
            'maintenance migrate' => ['POST', '/admin/maintenance/migrate', 'admin'],
            'maintenance clear cache' => ['POST', '/admin/maintenance/clear-cache', 'admin'],
        ];
    }

    #[DataProvider('pages')]
    public function test_page_access_follows_role_table(string $method, string $uri, string $level): void
    {
        $s = BookingScenario::create();

        foreach (['admin', 'verwalter', 'team', 'gast'] as $role) {
            $response = $this->actingAs($s->user($role))->call($method, $this->uri($uri, $s, $role));

            if ($this->allowed($role, $level)) {
                $this->assertNotContains($response->getStatusCode(), [401, 403], "$role darf $uri aufrufen");
                $this->assertLessThan(500, $response->getStatusCode(), "$uri liefert für $role einen Serverfehler");
                $this->assertFalse($this->isLoginRedirect($response), "$role darf $uri aufrufen");
            } else {
                $this->assertSame(403, $response->getStatusCode(), "$role darf $uri nicht aufrufen");
            }
        }
    }

    #[DataProvider('actions')]
    public function test_actions_are_forbidden_below_required_role(string $method, string $uri, string $level): void
    {
        $s = BookingScenario::create();

        foreach (['verwalter', 'team', 'gast'] as $role) {
            if ($this->allowed($role, $level)) {
                continue;
            }
            $response = $this->actingAs($s->user($role))->call($method, $this->uri($uri, $s, $role), [
                'role_id' => config('status.role_Administrator'),
                'password' => 'gehackt123',
                'name' => 'Geändert',
            ]);

            $this->assertSame(403, $response->getStatusCode(), "$role darf $method $uri nicht ausführen");
        }

        // Nichts davon darf etwas verändert haben.
        $this->assertNotSame('Geändert', $s->event->fresh()?->name);
        $this->assertTrue($s->team->fresh()->role_id === config('status.role_Team'));
    }

    #[DataProvider('pages')]
    public function test_guests_are_redirected_to_login(string $method, string $uri): void
    {
        $s = BookingScenario::create();

        $response = $this->call($method, $this->uri($uri, $s, 'admin'));

        $this->assertTrue($this->isLoginRedirect($response), "Gast wird bei $uri zum Login geschickt");
    }

    public function test_navigation_shows_only_allowed_entries(): void
    {
        $s = BookingScenario::create();
        $links = [
            'team' => [route('admin.events.index'), route('admin.bookings'), route('users.index')],
            'verwalter' => [route('admin.contacts.index'), route('newsletter.index'), route('rooms.index'), route('applications.index')],
            'admin' => [route('homepages.index'), route('positions.index'), route('faqs.index')],
        ];

        foreach (['admin', 'verwalter', 'team'] as $role) {
            $response = $this->actingAs($s->user($role))->get('/admin')->assertOk();
            foreach ($links as $level => $hrefs) {
                foreach ($hrefs as $href) {
                    $this->allowed($role, $level)
                        ? $response->assertSee('href="'.$href.'"', false)
                        : $response->assertDontSee('href="'.$href.'"', false);
                }
            }
        }
    }

    public function test_team_sees_bookings_without_edit_controls(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('team'))->get('/admin/events')
            ->assertOk()
            ->assertDontSee(route('admin.events.create'))
            ->assertDontSee(route('admin.homepages.comment_update', 1));
        $this->actingAs($s->user('verwalter'))->get('/admin/events')
            ->assertOk()
            ->assertSee(route('admin.events.create'));
    }

    public function test_registered_user_without_role_is_forbidden(): void
    {
        $s = BookingScenario::create();

        // Ohne Rolle stürzte isAdmin() mit "array offset on null" ab (500).
        $this->actingAs($s->user('registered'))->get('/admin')->assertForbidden();
    }

    public function test_inactive_team_member_is_forbidden(): void
    {
        $s = BookingScenario::create();
        $s->team->update(['is_active' => false]);

        $this->actingAs($s->user('team'))->get('/admin')->assertForbidden();
    }

    private function allowed(string $role, string $level): bool
    {
        return (self::LEVELS[$role] ?? 0) >= self::LEVELS[$level];
    }

    private function uri(string $uri, BookingScenario $s, string $role): string
    {
        return strtr($uri, [
            '{event}' => $s->event->id,
            '{contact}' => $s->contact->id,
            '{application}' => $s->application->id,
            '{room}' => $s->room->id,
            '{checkpoint}' => $s->checkpoint->id,
            '{newsletter}' => $s->newsletter->id,
            '{position}' => $s->pricelistPosition->id,
            '{self}' => $s->user($role)->id,
            '{other}' => 1,
        ]);
    }

    private function isLoginRedirect($response): bool
    {
        return $response->isRedirect() && str_ends_with($response->headers->get('Location'), '/login');
    }
}
