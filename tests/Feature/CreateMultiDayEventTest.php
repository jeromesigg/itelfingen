<?php

namespace Tests\Feature;

use App\Enums\BookingState;
use App\Enums\ContractState;
use App\Models\Event;
use App\Notifications\EventCreatedNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Öffentliches Buchungsformular auf der Startseite (POST event/create).
 */
class CreateMultiDayEventTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Notification::fake();
        // Der Spam-Schutz wird in einem eigenen Test geprüft.
        config(['honeypot.enabled' => false]);
    }

    private function bookingData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Muster',
            'firstname' => 'Max',
            'group' => 'Cevi Zürich',
            'street' => 'Dorfstrasse',
            'house_number' => '1',
            'zipcode' => 8000,
            'city' => 'Zürich',
            'email' => 'max@example.com',
            'telephone' => '079 123-45 67',
            'terms' => 'on',
            'start_date' => now()->addMonth()->toDateString(),
            'end_date' => now()->addMonth()->addDays(2)->toDateString(),
            'total_days' => 2,
            'total_people' => 10,
            'total_amount' => 900,
            'discount' => 0,
            'positions' => [30 => 6, 40 => 4],
        ], $overrides);
    }

    public function test_multi_day_booking_creates_event_with_positions(): void
    {
        $this->from(route('home'))->post('/event/create', $this->bookingData())
            ->assertRedirect(route('home').'#booking')
            ->assertSessionHas('success_event');

        $event = Event::sole();
        $this->assertSame('Cevi Zürich', $event->group_name);
        $this->assertSame(8000, $event->plz);
        $this->assertSame('0791234567', $event->phonenumber_query);
        $this->assertSame(BookingState::Neu, $event->event_status_id);
        $this->assertSame(ContractState::Offen, $event->contract_status_id);
        $this->assertNotNull($event->uuid);

        // Hausmiete 1x, Personen wie erfasst, Reinigung mit 0
        $amounts = $event->positions()->with('pricelist_position')->get()
            ->mapWithKeys(fn ($p) => [$p->pricelist_position->bexio_code => $p->amount])
            ->sortKeys()->all();
        $this->assertSame([20 => 1, 30 => 6, 40 => 4, 210 => 0], $amounts);

        Notification::assertSentTo($event, EventCreatedNotification::class);
    }

    public function test_marketing_comment_is_stored_and_shown_to_manager(): void
    {
        $this->from(route('home'))->post('/event/create', $this->bookingData([
            'marketing_comment' => 'Empfehlung von Freunden',
        ]));

        $event = Event::sole();
        $this->assertSame('Empfehlung von Freunden', $event->marketing_comment);

        $s = BookingScenario::create();
        $this->actingAs($s->user('verwalter'))->get("/admin/events/{$event->id}/edit")
            ->assertOk()
            ->assertSee('Empfehlung von Freunden');
    }

    public function test_booking_form_cannot_set_internal_fields(): void
    {
        $this->from(route('home'))->post('/event/create', $this->bookingData([
            'code' => 1234,
            'comment_intern' => 'von aussen',
            'external' => 1,
            'user_id' => 1,
            'bexio_invoice_id' => 99,
            'glutz_user_id' => 'fremd',
            'event_status_id' => BookingState::Bestaetigt->value,
            'cleaning_mail' => 1,
        ]))->assertRedirect(route('home').'#booking');

        $event = Event::sole();
        $this->assertNull($event->code);
        $this->assertNull($event->comment_intern);
        $this->assertFalse($event->external);
        $this->assertNull($event->user_id);
        $this->assertNull($event->bexio_invoice_id);
        $this->assertNull($event->glutz_user_id);
        $this->assertFalse($event->cleaning_mail);
        $this->assertSame(BookingState::Neu, $event->event_status_id);
    }

    public function test_one_day_booking_uses_day_rate(): void
    {
        $date = now()->addMonth()->toDateString();

        $this->from(route('home'))->post('/event/create', $this->bookingData([
            'total_days' => 0,
            'date' => $date,
        ]))->assertRedirect(route('home').'#booking');

        $event = Event::sole();
        $this->assertSame($date, Carbon::parse($event->start_date)->toDateString());
        $this->assertSame($date, Carbon::parse($event->end_date)->toDateString());
        $this->assertSame(1, $event->total_days);
        $this->assertEqualsCanonicalizing(
            [20, 50, 210],
            $event->positions()->with('pricelist_position')->get()->pluck('pricelist_position.bexio_code')->all()
        );
    }

    public function test_invalid_zipcode_is_rejected(): void
    {
        $this->from(route('home'))->post('/event/create', $this->bookingData(['zipcode' => 123]))
            ->assertRedirect(route('home').'#booking')
            ->assertSessionHasErrors('zipcode', null, 'event');

        $this->assertSame(0, Event::count());
    }

    public function test_more_than_19_people_is_rejected(): void
    {
        $this->from(route('home'))->post('/event/create', $this->bookingData(['total_people' => 20]))
            ->assertSessionHasErrors('total_people', null, 'event');

        $this->assertSame(0, Event::count());
    }
}
