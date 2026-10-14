<?php

namespace Tests\Feature;

use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Login in den Kundenbereich mit Buchungsnummer und PLZ (bookings/login), Rate-Limit "booking-login".
 */
class CustomerLoginTest extends TestCase
{
    public function test_booking_number_and_zipcode_lead_to_booking(): void
    {
        $s = BookingScenario::create();
        $event = $s->event;

        $this->post('/bookings/login', ['id' => $event->number(), 'plz' => $event->plz])
            ->assertRedirect(route('bookings.uuid', $event->uuid));
    }

    public function test_wrong_zipcode_is_rejected(): void
    {
        $s = BookingScenario::create();

        $this->from('/bookings/login')
            ->post('/bookings/login', ['id' => $s->event->id, 'plz' => $s->event->plz + 1])
            ->assertRedirect('/bookings/login');

        // Die Meldung muss auf der Login-Seite erscheinen (bisher landete sie in einem Fehler-Bag, den die Seite nie zeigte).
        $this->get('/bookings/login')
            ->assertSee('Die eingegebenen Daten sind nicht korrekt.')
            ->assertSee('value="'.$s->event->id.'"', false);
    }

    public function test_missing_fields_are_rejected_without_server_error(): void
    {
        BookingScenario::create();

        // Fehlte "id", griff der Controller auf einen nicht vorhandenen Array-Schlüssel zu (500).
        $this->from('/bookings/login')->post('/bookings/login', [])->assertRedirect('/bookings/login');
        $this->from('/bookings/login')->post('/bookings/login', ['id' => 1])->assertRedirect('/bookings/login');
    }

    public function test_guessing_is_limited_to_three_attempts_per_booking_and_hour(): void
    {
        $s = BookingScenario::create();
        $event = $s->event;

        foreach (range(1, 3) as $attempt) {
            // Verschiedene IPs, damit nur das Limit pro Buchungsnummer greift
            $this->withServerVariables(['REMOTE_ADDR' => "10.0.0.$attempt"])
                ->post('/bookings/login', ['id' => $event->id, 'plz' => 1000 + $attempt])
                ->assertRedirect();
        }

        $this->withServerVariables(['REMOTE_ADDR' => '10.0.0.9'])
            ->post('/bookings/login', ['id' => $event->id, 'plz' => $event->plz])
            ->assertTooManyRequests();
    }

    public function test_unknown_booking_uuid_is_not_found(): void
    {
        BookingScenario::create();

        $this->get('/bookings/00000000-0000-0000-0000-000000000000')->assertNotFound();
    }
}
