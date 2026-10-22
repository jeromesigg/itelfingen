<?php

namespace Tests\Feature;

use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Buchung erstellen/bearbeiten im Admin nach dem Wechsel von Bootstrap zu Tailwind:
 * keine Bootstrap-Gitterklassen mehr, und alle Felder/IDs, auf die das JavaScript der Seiten zugreift, sind noch da.
 */
class AdminEventFormLayoutTest extends TestCase
{
    // Ganze Klassennamen (durch Leerzeichen getrennt), damit z. B. lg:flex-row nicht als "row" zählt
    private const BOOTSTRAP = '/class="(?:[^"]*\s)?(form-row|form-group|col(-(xl|md|sm|lg))?-\d+|row|table|container)(\s[^"]*)?"/';

    public function test_edit_page_keeps_all_fields_used_by_javascript(): void
    {
        $s = BookingScenario::create();
        $event = $s->event;
        $event->update(['external' => true, 'cleaning_mail' => false]);

        $html = $this->actingAs($s->user('verwalter'))->get("/admin/events/{$event->id}/edit")->assertOk()->getContent();

        foreach (['start_date', 'end_date', 'name', 'firstname', 'group_name', 'email', 'telephone', 'street', 'house_number',
            'plz', 'city', 'total_people', 'total_days', 'discount', 'total', 'early_checkin', 'late_checkout', 'foreign_key',
            'contract_status_id', 'user_id', 'code', 'comment', 'comment_intern', 'marketing_comment', 'additional_text',
            'DeleteForm', 'cleaning_mail', 'cleaning_mail_address', 'cleaning_mail_text', 'import-text'] as $id) {
            $this->assertStringContainsString('id="'.$id.'"', $html, "id=\"$id\" fehlt");
        }
        foreach ($event->positions as $position) {
            $this->assertStringContainsString('id="position_'.$position->id.'"', $html);
            $this->assertStringContainsString('name="positions['.$position->id.']"', $html);
        }
        $this->assertActionsAndConfig($html, ['create-code', 'reminder-text', 'cleaning-mail', 'import']);
        $this->assertStringContainsString('confirm-delete', $html);
        $this->assertStringContainsString(route('bookings.uuid', $event->uuid), $html);
        $this->assertStringContainsString(route('events.downloadParking', $event->uuid), $html);
        $this->assertDoesNotMatchRegularExpression(self::BOOTSTRAP, $this->content($html));
    }

    public function test_edit_page_shows_header_with_number_and_status(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('verwalter'))->get("/admin/events/{$s->confirmedEvent->id}/edit")
            ->assertOk()
            ->assertSee('Buchung '.str_pad($s->confirmedEvent->id, 5, '0', STR_PAD_LEFT))
            ->assertSee('href="'.route('admin.events.index').'"', false)
            ->assertSee($s->confirmedEvent->contract_status->name);
    }

    public function test_create_page_keeps_all_fields_used_by_javascript(): void
    {
        $s = BookingScenario::create();

        $html = $this->actingAs($s->user('verwalter'))->get('/admin/events/create')->assertOk()->getContent();

        foreach (['import-text', 'start_date', 'end_date', 'name', 'firstname', 'group_name', 'email', 'telephone', 'street',
            'house_number', 'plz', 'city', 'comment', 'event_status_id', 'comment_intern', 'foreign_key', 'total_people',
            'total_days', 'discount', 'total', 'user_id', 'code'] as $id) {
            $this->assertStringContainsString('id="'.$id.'"', $html, "id=\"$id\" fehlt");
        }
        $this->assertActionsAndConfig($html, ['intern', 'import']);
        $this->assertStringContainsString('action="'.route('admin.events.store').'"', $html);
        $this->assertDoesNotMatchRegularExpression(self::BOOTSTRAP, $this->content($html));
    }

    /**
     * Konfiguration für resources/js/admin/event-form.js aus dem HTML der Seite.
     *
     * @return array{positions: list<array{input: string, bexio_code: int, price: float}>, calendarUrl: string, parseUrl: string}
     */
    public static function eventFormConfig(string $html): array
    {
        preg_match('#<script type="application/json" id="event-form-config">(.*?)</script>#s', $html, $match);

        return json_decode($match[1] ?? 'null', true) ?? [];
    }

    /**
     * Buttons mit data-action und Felder der Positionen, auf die das Skript zugreift.
     *
     * @param  list<string>  $actions
     */
    private function assertActionsAndConfig(string $html, array $actions): void
    {
        foreach ($actions as $action) {
            $this->assertStringContainsString('data-action="'.$action.'"', $html, "Button $action fehlt");
        }
        $config = self::eventFormConfig($html);
        $this->assertNotEmpty($config['positions'] ?? [], 'Konfiguration der Positionen fehlt');
        foreach ($config['positions'] as $position) {
            $this->assertStringContainsString('id="'.$position['input'].'"', $html, "Feld {$position['input']} fehlt");
        }
        $this->assertSame(route('events.parse'), $config['parseUrl']);
    }

    /** Nur den Inhaltsbereich der Seite prüfen, nicht das Admin-Layout. */
    private function content(string $html): string
    {
        $start = strpos($html, '<h3');

        return substr($html, $start, strpos($html, '<script type="application/json" id="event-form-config">', $start) - $start);
    }
}
