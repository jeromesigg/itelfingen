<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\Event;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Fachliche Antworten der API (Kalender, Telefonanlage, Verfügbarkeit) und der Ortssuche im Buchungsformular.
 * Zugriffsschutz: Security/ApiTokenTest.
 */
class PublicApiTest extends TestCase
{
    private const TOKEN = 'test-api-token';

    public function test_city_search_returns_matches(): void
    {
        City::create(['name' => 'Itelfingen', 'plz' => 8000]);

        $this->getJson('/event/searchajaxcity?term=Itel')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Itelfingen', 'plz' => 8000]);
    }

    public function test_city_search_without_matches_returns_empty_entry(): void
    {
        // Ohne Treffer war $data nicht definiert: 500.
        $this->getJson('/event/searchajaxcity?term=Nirgendwo')
            ->assertOk()
            ->assertExactJson(['name' => '', 'plz' => '', 'id' => '']);
    }

    public function test_events_api_lists_upcoming_own_bookings_only(): void
    {
        $s = BookingScenario::create();
        $external = Event::factory()->confirmed()->create(['external' => true]);

        $ids = collect($this->getJson('/api/events/'.self::TOKEN)->assertOk()->json())->pluck('id');

        // Neu und bestätigt in der Zukunft, ohne Storno, Vergangenheit und externe Buchungen (z. B. von Plattformen).
        $this->assertEqualsCanonicalizing([$s->event->id, $s->confirmedEvent->id], $ids->all());
        $this->assertNotContains($external->id, $ids);
    }

    public function test_events_api_marks_confirmed_bookings_as_occupied(): void
    {
        $s = BookingScenario::create();

        $events = collect($this->getJson('/api/events/'.self::TOKEN)->json())->keyBy('id');

        $this->assertSame('occupied', $events[$s->confirmedEvent->id]['occupancy_type']);
        $this->assertSame('tentative', $events[$s->event->id]['occupancy_type']);
    }

    public function test_is_free_ignores_new_and_cancelled_bookings(): void
    {
        Event::factory()->create(['start_date' => today(), 'end_date' => today()->addDay()]);
        Event::factory()->cancelled()->create(['start_date' => today(), 'end_date' => today()->addDay()]);

        $this->getJson('/api/isFree/'.self::TOKEN.'/3')->assertOk()->assertJson(['isFree' => true]);
    }

    public function test_is_free_is_false_with_confirmed_booking(): void
    {
        BookingScenario::create();
        Event::factory()->confirmed()->create(['start_date' => today()->addDay(), 'end_date' => today()->addDays(2)]);

        $this->getJson('/api/isFree/'.self::TOKEN.'/3')->assertOk()->assertJson(['isFree' => false]);
    }

    public function test_phone_number_lookup_finds_booking_by_local_and_international_number(): void
    {
        $s = BookingScenario::create();
        $s->event->update(['telephone' => '079 123 45 67', 'phonenumber_query' => '0791234567']);

        foreach (['0791234567', '+41791234567', '791234567'] as $number) {
            $this->getJson('/api/phone_number/'.self::TOKEN.'/number/'.urlencode($number))
                ->assertOk()
                ->assertJson(['name' => $s->event->name]);
        }
    }

    public function test_phone_number_lookup_does_not_treat_input_as_pattern(): void
    {
        BookingScenario::create();

        // % und _ sind LIKE-Platzhalter: "%" hätte die erste beliebige Buchung geliefert.
        foreach (['%', '_', '07%'] as $pattern) {
            $this->getJson('/api/phone_number/'.self::TOKEN.'/number/'.urlencode($pattern))->assertNotFound();
        }
    }

    public function test_phone_number_lookup_requires_enough_digits(): void
    {
        $s = BookingScenario::create();
        $s->event->update(['phonenumber_query' => '0791234567']);

        $this->getJson('/api/phone_number/'.self::TOKEN.'/number/1')->assertNotFound();
    }
}
