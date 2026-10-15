<?php

namespace Tests\Feature;

use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Bewerbung im Admin anzeigen (admin/applications/{id}) und ablehnen.
 */
class AdminApplicationTest extends TestCase
{
    private function show(BookingScenario $s)
    {
        return $this->actingAs($s->user('verwalter'))->get("/admin/applications/{$s->application->id}")->assertOk();
    }

    public function test_shows_contact_and_application_details(): void
    {
        $s = BookingScenario::create();
        $s->application->update([
            'firstname' => 'Erika', 'name' => 'Muster', 'organisation' => 'Cevi Zürich',
            'street' => 'Dorfstrasse', 'house_number' => '2', 'plz' => 8000, 'city' => 'Zürich',
            'email' => 'erika@example.com', 'telephone' => '079 765 43 21',
            'why' => "Erste Zeile\nZweite Zeile", 'comment' => 'Bemerkung',
        ]);

        $this->show($s)
            ->assertSee('Erika Muster')
            ->assertSee('Cevi Zürich')
            ->assertSee('Dorfstrasse 2')
            // Bisher stand dort $application['zipcode'], die Spalte heisst aber plz: die PLZ fehlte immer.
            ->assertSee('8000 Zürich')
            ->assertSee('href="mailto:erika@example.com"', false)
            ->assertSee('href="tel:0797654321"', false)
            ->assertSee("Erste Zeile\nZweite Zeile", false)
            ->assertSee('Bemerkung');
    }

    public function test_open_application_can_be_refused(): void
    {
        $s = BookingScenario::create();

        $this->show($s)
            ->assertSee('Offen')
            ->assertSee('action="'.route('applications.refuse', $s->application).'"', false);

        $this->actingAs($s->user('verwalter'))->post(route('applications.refuse', $s->application))
            ->assertRedirect('/admin/applications');
        $this->assertTrue((bool) $s->application->fresh()->refuse);
    }

    public function test_refused_or_invoiced_application_shows_status_without_refuse_button(): void
    {
        $s = BookingScenario::create();
        $s->application->update(['refuse' => true]);
        $this->show($s)->assertSee('Abgelehnt')->assertDontSee(route('applications.refuse', $s->application));

        $s->application->update(['refuse' => false, 'invoice_send' => true, 'bexio_invoice_id' => 77]);
        $this->show($s)
            ->assertSee('Rechnung versendet')
            ->assertSee('https://office.bexio.com/index.php/kb_invoice/show/id/77')
            ->assertDontSee(route('applications.refuse', $s->application));
    }

    public function test_user_input_is_escaped(): void
    {
        $s = BookingScenario::create();
        $s->application->update(['name' => '<img src=x onerror=alert(1)>', 'why' => '<script>alert(1)</script>']);

        $html = $this->show($s)->getContent();

        $this->assertStringNotContainsString('<img src=x', $html);
        $this->assertStringNotContainsString('<script>alert(1)', $html);
    }
}
