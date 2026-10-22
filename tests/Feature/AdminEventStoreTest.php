<?php

namespace Tests\Feature;

use App\Enums\BookingState;
use App\Enums\ContractState;
use App\Models\Event;
use App\Services\GoogleCalendarService;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Formular "Buchung erstellen" im Admin (POST admin/events), wie es der Browser schickt.
 */
class AdminEventStoreTest extends TestCase
{
    private BookingScenario $s;

    protected function setUp(): void
    {
        parent::setUp();

        $this->s = BookingScenario::create();
    }

    private function store(array $overrides = [])
    {
        return $this->actingAs($this->s->user('verwalter'))
            ->from('/admin/events/create')
            ->post('/admin/events', array_merge([
                'start_date' => '2030-11-11',
                'end_date' => '2030-11-14',
                'name' => 'Muster',
                'firstname' => 'Max',
                'group_name' => 'Cevi Zug',
                'email' => 'max@example.com',
                'telephone' => '079 123-45 67',
                'street' => 'Dorfstrasse',
                'house_number' => '1',
                'plz' => '6300',
                'city' => 'Zug',
                // Hausmiete als verstecktes Feld (Menge 1), Personen als Eingaben, Schlüssel = bexio_code
                'positions' => [20 => 1, 30 => 6, 40 => 4],
                'total_people' => 10,
                'total_days' => 3,
                'discount' => '',
                'event_status_id' => BookingState::Neu->value,
                'foreign_key' => '',
                'user_id' => $this->s->verwalter->id,
                'code' => '',
                'comment' => 'Mit Hund',
                'comment_intern' => 'Stammgäste',
            ], $overrides));
    }

    public function test_creates_booking_with_positions_and_opens_it(): void
    {
        $response = $this->store();

        $event = Event::where('email', 'max@example.com')->sole();
        $response->assertRedirect(route('admin.events.edit', $event));
        $this->assertSame('Muster', $event->name);
        $this->assertSame(ContractState::Offen, $event->contract_status_id);
        $this->assertSame(BookingState::Neu, $event->event_status_id);
        $this->assertSame('0791234567', $event->phonenumber_query);
        $this->assertFalse((bool) $event->external);
        $this->assertNotEmpty($event->uuid);

        $amounts = $event->positions()->with('pricelist_position')->get()
            ->mapWithKeys(fn ($position) => [$position->pricelist_position->bexio_code => $position->amount])->sortKeys()->all();
        // Hausmiete, Personen und Parkplätze (immer, mit 0)
        $this->assertEquals([20 => 1, 30 => 6, 40 => 4, 210 => 0], $amounts);
    }

    public function test_external_booking_number_marks_booking_as_external(): void
    {
        $this->store(['foreign_key' => 'AB-77']);

        $event = Event::where('email', 'max@example.com')->sole();
        $this->assertTrue((bool) $event->external);
        $this->assertSame('AB-77', $event->foreign_key);
    }

    public function test_internal_booking_without_nights_is_one_day_and_goes_to_calendar(): void
    {
        $this->store(['event_status_id' => BookingState::Eigene->value, 'total_days' => 0, 'end_date' => '2030-11-20', 'positions' => [20 => 1]]);

        $event = Event::where('email', 'max@example.com')->sole();
        $this->assertSame('2030-11-11', substr($event->start_date, 0, 10));
        $this->assertSame('2030-11-11', substr($event->end_date, 0, 10));
        $this->assertSame(1, (int) $event->total_days);
        $this->assertCount(1, app(GoogleCalendarService::class)->bookings);
    }

    public function test_guest_booking_is_not_added_to_calendar_yet(): void
    {
        $this->store();

        $this->assertCount(0, app(GoogleCalendarService::class)->bookings);
    }

    public function test_without_phone_and_positions(): void
    {
        $data = ['telephone' => null];
        $this->store($data + ['positions' => null])->assertSessionHasNoErrors();

        $event = Event::where('email', 'max@example.com')->sole();
        $this->assertSame('', $event->phonenumber_query);
    }

    public function test_incomplete_form_is_rejected_without_server_error(): void
    {
        $count = Event::count();

        $this->actingAs($this->s->user('verwalter'))
            ->from('/admin/events/create')
            ->post('/admin/events', [])
            ->assertRedirect('/admin/events/create')
            ->assertSessionHasErrors(['start_date', 'end_date', 'name', 'email', 'street', 'plz', 'city', 'total_days', 'user_id', 'event_status_id']);

        $this->assertSame($count, Event::count());
    }

    public function test_end_before_start_is_rejected(): void
    {
        $this->store(['start_date' => '2030-11-14', 'end_date' => '2030-11-11'])->assertSessionHasErrors('end_date');

        $this->assertSame(0, Event::where('email', 'max@example.com')->count());
    }

    public function test_team_cannot_create_bookings(): void
    {
        $this->actingAs($this->s->user('team'))->post('/admin/events', ['name' => 'X'])->assertForbidden();
    }
}
