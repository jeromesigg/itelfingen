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
        foreach (['Total_Change()', 'CreateAPICode()', 'PrepareReminderMail()', 'PrepareMail()', 'importExtern()'] as $handler) {
            $this->assertStringContainsString($handler.'"', $html, "$handler fehlt");
        }
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
        foreach (['createIntern()', 'createExtern()', 'Total_Change()'] as $handler) {
            $this->assertStringContainsString($handler.'"', $html, "$handler fehlt");
        }
        $this->assertStringContainsString('action="'.route('admin.events.store').'"', $html);
        $this->assertDoesNotMatchRegularExpression(self::BOOTSTRAP, $this->content($html));
    }

    /** Nur den Inhaltsbereich der Seite prüfen, nicht das Admin-Layout. */
    private function content(string $html): string
    {
        $start = strpos($html, '<h3');

        return substr($html, $start, strpos($html, '<script type="module">', $start) - $start);
    }
}
