<?php

namespace Tests\Feature;

use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Aktionen in den Admin-Tabellen (DataTable.vue): Die Tabelle schickt sie per fetch (Accept: application/json)
 * und ersetzt mit der Antwort nur die betroffene Zeile. Ohne JSON bleibt die Weiterleitung wie bisher.
 */
class AdminTableActionsTest extends TestCase
{
    public function test_contact_row_offers_done_action_until_done(): void
    {
        $s = BookingScenario::create();

        $row = $this->actingAs($s->user('verwalter'))->getJson('/contacts/datatable?status=Offen')->assertOk()->json('data.0');

        $this->assertSame($s->contact->id, $row['id']);
        $this->assertSame([['label' => 'Bearbeitet', 'url' => route('contacts.done', $s->contact)]], $row['actions']);
    }

    public function test_marking_contact_done_returns_updated_row(): void
    {
        $s = BookingScenario::create();
        $user = $s->user('verwalter');

        $response = $this->actingAs($user)->postJson(route('contacts.done', $s->contact))->assertOk();

        $this->assertSame('Anfrage als bearbeitet markiert.', $response->json('message'));
        $this->assertSame($s->contact->id, $response->json('row.id'));
        $this->assertSame('Ja', $response->json('row.done'));
        $this->assertSame($user->username, $response->json('row.user'));
        $this->assertSame([], $response->json('row.actions'));
        $this->assertTrue((bool) $s->contact->fresh()->done);
    }

    public function test_marking_contact_done_without_json_still_redirects(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('verwalter'))->post(route('contacts.done', $s->contact))->assertRedirect('/admin/contacts');
    }

    public function test_application_row_offers_refuse_action_with_confirmation(): void
    {
        $s = BookingScenario::create();

        $row = $this->actingAs($s->user('verwalter'))->getJson('/applications/datatable')->assertOk()->json('data.0');

        $this->assertSame($s->application->id, $row['id']);
        $this->assertSame('Ablehnen', $row['actions'][0]['label']);
        $this->assertSame(route('applications.refuse', $s->application), $row['actions'][0]['url']);
        $this->assertSame('danger', $row['actions'][0]['variant']);
        $this->assertStringContainsString($s->application->name, $row['actions'][0]['confirm']);
    }

    public function test_refusing_application_returns_updated_row_without_actions(): void
    {
        $s = BookingScenario::create();

        $response = $this->actingAs($s->user('verwalter'))->postJson(route('applications.refuse', $s->application))->assertOk();

        $this->assertSame('Bewerbung abgelehnt.', $response->json('message'));
        $this->assertSame('Ja', $response->json('row.refuse'));
        $this->assertSame([], $response->json('row.actions'));
        $this->assertTrue((bool) $s->application->fresh()->refuse);
    }

    public function test_invoiced_application_has_no_refuse_action(): void
    {
        $s = BookingScenario::create();
        $s->application->update(['invoice_send' => true]);

        $row = $this->actingAs($s->user('verwalter'))->getJson('/applications/datatable')->assertOk()->json('data.0');

        $this->assertSame([], $row['actions']);
    }

    public function test_team_cannot_run_table_actions(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('team'))->postJson(route('contacts.done', $s->contact))->assertForbidden();
        $this->actingAs($s->user('team'))->postJson(route('applications.refuse', $s->application))->assertForbidden();
        $this->assertFalse((bool) $s->contact->fresh()->done);
        $this->assertFalse((bool) $s->application->fresh()->refuse);
    }

    public function test_booking_dates_sort_chronologically(): void
    {
        // DataTable.vue sortiert im Browser nach "sort"; spätere Daten müssen grössere Werte haben
        $s = BookingScenario::create();
        $s->event->update(['start_date' => '2030-11-11', 'end_date' => '2030-11-13']);
        $s->confirmedEvent->update(['start_date' => '2030-12-11', 'end_date' => '2030-12-13']);

        $sorts = collect($this->actingAs($s->user('verwalter'))->getJson('/events/datatable?status=Alle&date=Alle')->assertOk()->json('data'))
            ->pluck('start_date.sort');

        // ISO-Datum: als Text verglichen chronologisch
        $this->assertContains('2030-11-11', $sorts);
        $this->assertContains('2030-12-11', $sorts);
        foreach ($sorts as $sort) {
            $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $sort);
        }
    }

    public function test_bookings_page_passes_contract_statuses_as_props(): void
    {
        $s = BookingScenario::create();

        $html = $this->actingAs($s->user('verwalter'))->get('/admin/events')->assertOk()->getContent();
        preg_match('/data-vue-component="events-table"\s+data-props="([^"]*)"/', $html, $match);
        $props = json_decode(html_entity_decode($match[1] ?? ''), true);

        $this->assertContains('Offen', $props['contractStatuses']);
        $this->assertStringNotContainsString('window.contractStatuses', $html);
    }
}
