<?php

namespace Tests\Feature\Security;

use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Eingaben aus den öffentlichen Formularen (Buchung, Kontakt, Bewerbung) erscheinen in den Admin-Tabellen.
 * Sie dürfen dort nie als HTML ausgeführt werden.
 */
class XssTest extends TestCase
{
    private const PAYLOAD = '<img src=x onerror=alert(1)>';

    public function test_booking_fields_are_escaped_in_events_table(): void
    {
        $s = BookingScenario::create();
        $s->event->update([
            'name' => self::PAYLOAD,
            'firstname' => self::PAYLOAD,
            'group_name' => self::PAYLOAD,
            'foreign_key' => self::PAYLOAD,
        ]);

        $json = $this->actingAs($s->user('verwalter'))
            ->getJson('/events/createDataTables?status=Alle&date=Alle&draw=1')
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('<img', $this->flatten($json));
    }

    public function test_events_table_does_not_expose_internal_fields(): void
    {
        $s = BookingScenario::create();
        $s->event->update(['code' => 4711, 'bexio_invoice_id' => 99]);

        $row = $this->actingAs($s->user('team'))
            ->getJson('/events/createDataTables?status=Alle&date=Alle&draw=1')
            ->assertOk()
            ->json('data.0');

        $this->assertArrayNotHasKey('code', $row);
        $this->assertArrayNotHasKey('bexio_invoice_id', $row);
        $this->assertArrayNotHasKey('uuid', $row);
    }

    public function test_team_sees_booking_names_without_edit_link(): void
    {
        $s = BookingScenario::create();

        $rows = $this->actingAs($s->user('team'))
            ->getJson('/events/createDataTables?status=Alle&date=Alle&draw=1')
            ->assertOk()
            ->json('data');

        $this->assertNotEmpty($rows);
        foreach ($rows as $row) {
            $this->assertStringNotContainsString('/edit', $row['name']);
        }
    }

    public function test_contact_name_is_escaped_in_contacts_table(): void
    {
        $s = BookingScenario::create();
        $s->contact->update(['name' => self::PAYLOAD]);

        $json = $this->actingAs($s->user('verwalter'))
            ->getJson('/contacts/createDataTables?done=Nein&draw=1')
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('<img', $this->flatten($json));
    }

    public function test_application_name_is_escaped_in_applications_table(): void
    {
        $s = BookingScenario::create();
        $s->application->update(['name' => self::PAYLOAD]);

        $json = $this->actingAs($s->user('verwalter'))
            ->getJson('/applications/createDataTables?draw=1')
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('<img', $this->flatten($json));
    }

    /**
     * JSON-Antwort als Text mit aufgelösten Escapes (\/ und <), damit "<img" sicher gefunden wird.
     */
    private function flatten(string $json): string
    {
        return json_encode(json_decode($json, true), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
