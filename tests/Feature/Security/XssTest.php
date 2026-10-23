<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\DataProvider;
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
            ->getJson('/events/datatable?status=Alle&date=Alle&draw=1')
            ->assertOk()
            ->getContent();

        $this->assertStringNotContainsString('<img', $this->flatten($json));
    }

    public function test_events_table_does_not_expose_internal_fields(): void
    {
        $s = BookingScenario::create();
        $s->event->update(['code' => 4711, 'bexio_invoice_id' => 99]);

        $row = $this->actingAs($s->user('team'))
            ->getJson('/events/datatable?status=Alle&date=Alle&draw=1')
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
            ->getJson('/events/datatable?status=Alle&date=Alle&draw=1')
            ->assertOk()
            ->json('data');

        $this->assertNotEmpty($rows);
        foreach ($rows as $row) {
            $this->assertStringNotContainsString('/edit', $row['name']);
        }
    }

    public function test_contact_fields_are_plain_text_and_actions_safe(): void
    {
        // Kontaktfelder kommen als Text (DataTable.vue rendert sie ohne v-html); Aktionen sind Daten, kein HTML.
        $s = BookingScenario::create();
        $s->contact->update(['name' => self::PAYLOAD, 'subject' => self::PAYLOAD, 'content' => self::PAYLOAD]);

        $row = $this->actingAs($s->user('verwalter'))
            ->getJson('/contacts/datatable?status=Offen')
            ->assertOk()
            ->json('data.0');

        $this->assertSame(self::PAYLOAD, $row['name']);
        $this->assertSame([['label' => 'Bearbeitet', 'url' => route('contacts.done', $s->contact)]], $row['actions']);
    }

    public function test_application_name_is_escaped_in_applications_table(): void
    {
        $s = BookingScenario::create();
        $s->application->update(['name' => self::PAYLOAD, 'why' => self::PAYLOAD]);

        $row = $this->actingAs($s->user('verwalter'))
            ->getJson('/applications/datatable')
            ->assertOk()
            ->json('data.0');

        $this->assertStringNotContainsString('<img', $row['name']);
        // Der Name steht im Bestätigungstext; DataTable.vue setzt ihn als SweetAlert titleText (Text, nicht HTML)
        $this->assertStringContainsString(self::PAYLOAD, $row['actions'][0]['confirm']);
    }

    public function test_newsletter_name_is_escaped(): void
    {
        // Newsletter-Namen stammen aus Buchungen und Bewerbungen
        $s = BookingScenario::create();
        $s->newsletter->update(['name' => self::PAYLOAD]);

        $row = $this->actingAs($s->user('verwalter'))->getJson('/newsletter/datatable')->assertOk()->json('data.0');

        $this->assertStringNotContainsString('<img', $row['name']);
    }

    /**
     * HTML-Spalten pro Tabelle. Jede andere Spalte rendert DataTable.vue als Text.
     * Wer hier eine Spalte ergänzt, muss sicherstellen, dass der Server ihre Werte escaped.
     */
    public static function htmlColumns(): array
    {
        return [
            'Buchungen' => ['App.vue', ['number', 'name', 'status']],
            // Aktionen sind Daten ({ label, url, … }), DataTable.vue rendert sie als Buttons
            'Kontakte' => ['ContactTable.vue', []],
            'Bewerbungen' => ['ApplicationTable.vue', ['name']],
            'FAQ' => ['FAQTable.vue', ['image', 'name']],
            'Newsletter' => ['NewsletterTable.vue', ['name']],
        ];
    }

    #[DataProvider('htmlColumns')]
    public function test_only_known_columns_are_rendered_as_html(string $component, array $expected): void
    {
        $source = file_get_contents(resource_path('js/components/'.$component));
        preg_match_all("/columnHelper\.accessor\('(\w+)', \{\s*meta: \{ html: true \}/", $source, $matches);

        $this->assertEqualsCanonicalizing($expected, $matches[1]);
    }

    public function test_data_table_uses_v_html_only_for_marked_columns(): void
    {
        // Tabelle und Handy-Karten rendern Zellen über DataTableCell.vue; nur dort darf v-html stehen.
        $cell = file_get_contents(resource_path('js/components/DataTableCell.vue'));

        $this->assertSame(1, substr_count($cell, 'v-html'));
        $this->assertMatchesRegularExpression('/v-(else-)?if="cell\.column\.columnDef\.meta\?\.html" v-html=/', $cell);
        foreach (['DataTable.vue', 'DataTableActions.vue'] as $component) {
            $this->assertStringNotContainsString('v-html', file_get_contents(resource_path('js/components/'.$component)), $component);
        }
    }

    /**
     * JSON-Antwort als Text mit aufgelösten Escapes (\/ und <), damit "<img" sicher gefunden wird.
     */
    private function flatten(string $json): string
    {
        return json_encode(json_decode($json, true), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
