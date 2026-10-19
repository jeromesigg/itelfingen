<?php

namespace Tests\Feature;

use App\Enums\BookingState;
use App\Models\Event;
use Illuminate\Support\Facades\Blade;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Kalender auf der Seite "Buchung bearbeiten": eine Woche vor bis eine Woche nach der Buchung,
 * mit den übrigen Buchungen in denselben Farben wie der Kalender der Buchungsübersicht.
 */
class BookingCalendarTest extends TestCase
{
    private function render(Event $event): string
    {
        return Blade::render('<x-booking-calendar :event="$event" />', ['event' => $event]);
    }

    /** CSS-Klasse der Tageszelle für ein Datum (Y-m-d). */
    private function dayClass(string $html, string $date): ?string
    {
        return preg_match('/<td class="(hk-agenda__day--\w+)[^"]*" data-date="'.$date.'"/', $html, $match) ? $match[1] : null;
    }

    public function test_shows_full_weeks_from_one_week_before_to_one_week_after(): void
    {
        BookingScenario::create();
        // Mi 11.11.2026 bis Fr 13.11.2026
        $event = Event::factory()->create(['start_date' => '2026-11-11', 'end_date' => '2026-11-13']);

        $html = $this->render($event);

        // Eine Woche vorher ist Mi 4.11., die Woche beginnt am Mo 2.11.; eine Woche nachher ist Fr 20.11., die Woche endet am So 22.11.
        $this->assertNull($this->dayClass($html, '2026-11-01'));
        $this->assertNotNull($this->dayClass($html, '2026-11-02'));
        $this->assertNotNull($this->dayClass($html, '2026-11-22'));
        $this->assertNull($this->dayClass($html, '2026-11-23'));
        $this->assertSame(3, substr_count($html, 'hk-agenda__week'));
    }

    public function test_current_booking_is_highlighted_with_half_days(): void
    {
        $event = Event::factory()->create([
            'start_date' => '2026-11-11', 'end_date' => '2026-11-13', 'early_checkin' => false, 'late_checkout' => false,
        ]);

        $html = $this->render($event);

        // Anreise am Nachmittag, Abreise am Vormittag: halbe Tage wie im Übersichtskalender
        $this->assertSame('hk-agenda__day--FS', $this->dayClass($html, '2026-11-11'));
        $this->assertSame('hk-agenda__day--SS', $this->dayClass($html, '2026-11-12'));
        $this->assertSame('hk-agenda__day--SF', $this->dayClass($html, '2026-11-13'));
        $this->assertSame('hk-agenda__day--FF', $this->dayClass($html, '2026-11-14'));
    }

    public function test_early_checkin_and_late_checkout_fill_whole_days(): void
    {
        $event = Event::factory()->create([
            'start_date' => '2026-11-11', 'end_date' => '2026-11-13', 'early_checkin' => true, 'late_checkout' => true,
        ]);

        $html = $this->render($event);

        $this->assertSame('hk-agenda__day--SS', $this->dayClass($html, '2026-11-11'));
        $this->assertSame('hk-agenda__day--SS', $this->dayClass($html, '2026-11-13'));
    }

    public function test_neighbouring_bookings_are_shown_and_linked(): void
    {
        $event = Event::factory()->create(['start_date' => '2026-11-11', 'end_date' => '2026-11-13']);
        $before = Event::factory()->confirmed()->create(['start_date' => '2026-11-06', 'end_date' => '2026-11-11', 'name' => 'Vorher']);
        $after = Event::factory()->create(['start_date' => '2026-11-15', 'end_date' => '2026-11-17', 'name' => 'Nachher']);

        $html = $this->render($event);

        // Bestätigt = B, Neu = P (Farben aus event_statuses)
        $this->assertSame('hk-agenda__day--BB', $this->dayClass($html, '2026-11-08'));
        // Wechseltag: Vormittag Abreise der vorherigen Buchung, Nachmittag Anreise dieser Buchung
        $this->assertSame('hk-agenda__day--BS', $this->dayClass($html, '2026-11-11'));
        $this->assertSame('hk-agenda__day--PP', $this->dayClass($html, '2026-11-16'));
        $this->assertStringContainsString(route('admin.events.edit', $before), $html);
        $this->assertStringContainsString(route('admin.events.edit', $after), $html);
        $this->assertStringContainsString('Vorher', $html);
    }

    public function test_cancelled_and_distant_bookings_are_not_shown(): void
    {
        $event = Event::factory()->create(['start_date' => '2026-11-11', 'end_date' => '2026-11-13']);
        $cancelled = Event::factory()->create([
            'start_date' => '2026-11-16', 'end_date' => '2026-11-18', 'event_status_id' => BookingState::Storniert,
        ]);
        $distant = Event::factory()->create(['start_date' => '2026-12-10', 'end_date' => '2026-12-12']);

        $html = $this->render($event);

        $this->assertSame('hk-agenda__day--FF', $this->dayClass($html, '2026-11-17'));
        $this->assertStringNotContainsString(route('admin.events.edit', $cancelled), $html);
        $this->assertStringNotContainsString(route('admin.events.edit', $distant), $html);
    }

    public function test_calendar_is_shown_on_edit_page(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('verwalter'))->get("/admin/events/{$s->event->id}/edit")
            ->assertOk()
            ->assertSee('data-date="'.$s->event->start_date.'"', false);
    }

    public function test_booking_names_are_escaped(): void
    {
        $event = Event::factory()->create(['start_date' => '2026-11-11', 'end_date' => '2026-11-13']);
        Event::factory()->create(['start_date' => '2026-11-15', 'end_date' => '2026-11-17', 'name' => '<b>x</b>']);

        $this->assertStringNotContainsString('<b>x</b>', $this->render($event));
    }

    private ?BookingScenario $scenario = null;

    private function preview(?Event $event, array $query, string $role = 'verwalter')
    {
        $s = $this->scenario ??= BookingScenario::create();

        return $this->actingAs($s->user($role))->get(route('events.calendar', $event).'?'.http_build_query($query));
    }

    public function test_preview_uses_dates_from_form_without_saving(): void
    {
        $event = Event::factory()->create(['start_date' => '2030-11-11', 'end_date' => '2030-11-13']);

        $html = $this->preview($event, [
            'start_date' => '2030-12-02', 'end_date' => '2030-12-04', 'early_checkin' => 1, 'late_checkout' => 0,
        ])->assertOk()->getContent();

        $this->assertSame('hk-agenda__day--SS', $this->dayClass($html, '2030-12-02'));
        $this->assertSame('hk-agenda__day--SF', $this->dayClass($html, '2030-12-04'));
        $this->assertNull($this->dayClass($html, '2030-11-11'));
        $this->assertSame('2030-11-11', $event->fresh()->start_date);
    }

    public function test_preview_rejects_invalid_dates(): void
    {
        $event = Event::factory()->create(['start_date' => '2030-11-11', 'end_date' => '2030-11-13']);

        $this->preview($event, ['start_date' => 'kein Datum', 'end_date' => '2030-11-13'])->assertStatus(302);
        $this->preview($event, ['start_date' => '2030-11-13', 'end_date' => '2030-11-11'])->assertStatus(302);
        // Sehr lange Zeiträume würden riesige Kalender erzeugen
        $this->preview($event, ['start_date' => '2030-01-01', 'end_date' => '2032-01-01'])->assertStatus(302);
    }

    public function test_preview_is_only_for_managers(): void
    {
        $event = Event::factory()->create(['start_date' => '2030-11-11', 'end_date' => '2030-11-13']);

        $this->preview($event, ['start_date' => '2030-11-11', 'end_date' => '2030-11-13'], 'team')->assertForbidden();
    }

    public function test_preview_without_booking_for_create_page(): void
    {
        $this->scenario = BookingScenario::create();
        $other = Event::factory()->confirmed()->create(['start_date' => '2030-11-06', 'end_date' => '2030-11-09']);
        $count = Event::count();

        $html = $this->preview(null, ['start_date' => '2030-11-11', 'end_date' => '2030-11-13'])->assertOk()->getContent();

        $this->assertSame('hk-agenda__day--FS', $this->dayClass($html, '2030-11-11'));
        $this->assertSame('hk-agenda__day--BB', $this->dayClass($html, '2030-11-07'));
        $this->assertStringContainsString(route('admin.events.edit', $other), $html);
        $this->assertSame($count, Event::count());
    }

    public function test_overview_calendar_links_bookings(): void
    {
        $s = BookingScenario::create();
        $event = Event::factory()->confirmed()->create([
            'start_date' => '2030-11-11', 'end_date' => '2030-11-13', 'early_checkin' => false, 'late_checkout' => false,
        ]);

        $html = $this->actingAs($s->user('verwalter'))->get('/admin/events')->assertOk()->getContent();
        preg_match('/data-vue-component="booking-calendar" data-props="([^"]*)"/', $html, $match);
        $props = json_decode(html_entity_decode($match[1] ?? ''), true);

        $this->assertSame('admin', $props['mode']);
        $this->assertSame(['am' => null, 'pm' => 'B', 'id' => $event->id], $props['days']['2030-11-11']);
        $this->assertSame(['am' => 'BD', 'pm' => null, 'id' => $event->id], $props['days']['2030-11-13']);
        $this->assertSame(route('admin.events.edit', $event), str_replace('__ID__', $event->id, $props['editUrl']));
    }

    public function test_create_page_shows_calendar_placeholder(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('verwalter'))->get('/admin/events/create')
            ->assertOk()
            ->assertSee('id="booking-calendar"', false)
            ->assertSee('Start und Ende wählen')
            ->assertSee(route('events.calendar'), false);
    }
}
