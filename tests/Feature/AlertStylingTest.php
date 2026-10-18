<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Meldungsboxen nach dem Wechsel von Bootstrap zu Tailwind/Flowbite: Bootstrap ist nicht mehr eingebunden,
 * Klassen wie "alert alert-danger" hätten kein Styling. Meldungen laufen über die Komponente <x-alert>.
 */
class AlertStylingTest extends TestCase
{
    public function test_no_view_uses_bootstrap_alert_classes(): void
    {
        $found = [];
        foreach (File::allFiles(resource_path('views')) as $file) {
            if (preg_match('/\balert-(danger|success|warning|info|dismissable)\b|data-dismiss="alert"/', $file->getContents())) {
                $found[] = $file->getRelativePathname();
            }
        }

        $this->assertSame([], $found, 'Bootstrap-Alert-Klassen ohne Styling in: '.implode(', ', $found));
    }

    public function test_alert_component_colors(): void
    {
        $danger = Blade::render('<x-alert>Fehler</x-alert>');
        $success = Blade::render('<x-alert type="success" id="ok" style="display: none">Gut</x-alert>');

        $this->assertStringContainsString('bg-red-50', $danger);
        $this->assertStringContainsString('role="alert"', $danger);
        $this->assertStringContainsString('bg-success-soft', $success);
        $this->assertStringContainsString('id="ok"', $success);
        $this->assertStringContainsString('display: none', $success);
    }

    public function test_booking_errors_are_shown_red(): void
    {
        Notification::fake();
        config(['honeypot.enabled' => false]);
        BookingScenario::create();

        $this->from(route('home'))->post('/event/create', ['name' => 'Muster']);

        $this->get('/')
            ->assertSee('Bitte akzeptiere die Hausordnung.')
            ->assertSee('bg-red-50', false);
    }

    public function test_contact_errors_are_shown_red(): void
    {
        config(['honeypot.enabled' => false]);
        BookingScenario::create();

        $this->from(route('home'))->post('/contacts', []);

        $html = $this->get('/')->getContent();
        preg_match('/<div[^>]*bg-red-50[^>]*>.*?<\/div>/s', $html, $boxes);
        $this->assertNotEmpty($boxes);
    }

    public function test_admin_form_errors_are_shown_red(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('admin'))->from("/admin/rooms/{$s->room->id}/edit")
            ->withSession(['errors' => (new ViewErrorBag)->put('default', new MessageBag(['name' => 'Name fehlt']))])
            ->get("/admin/rooms/{$s->room->id}/edit")
            ->assertSee('Name fehlt')
            ->assertSee('bg-red-50', false);
    }
}
