<?php

namespace Tests\Feature;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Bearbeiten-Formulare wählen in Auswahlfeldern den gespeicherten Wert vor.
 * Ohne Vorauswahl zeigt der Browser die erste Option, und Speichern setzt still einen anderen Wert.
 * Wichtig für Spalten mit Enum-Cast (Status, Rolle, Archiv): die Select-Komponente vergleicht mit ==.
 */
class FormPreselectionTest extends TestCase
{
    /**
     * Pro Formular: Objekt vorbereiten (Wert ≠ erste Option) und URI liefern, Feld, erwarteter Wert.
     */
    public static function forms(): array
    {
        $archived = fn (string $property, string $uri) => function (BookingScenario $s) use ($property, $uri) {
            $s->{$property}->forceFill(['archive_status_id' => 2])->save();

            return str_replace('{id}', $s->{$property}->id, $uri);
        };

        return [
            'Buchung: Vertragsstatus' => [function (BookingScenario $s) {
                $s->event->forceFill(['contract_status_id' => 10])->save();

                return "/admin/events/{$s->event->id}/edit";
            }, 'contract_status_id', '10'],
            'Benutzer: Rolle' => [fn (BookingScenario $s) => "/admin/users/{$s->verwalter->id}/edit", 'role_id', '5'],
            'Benutzer: Archiv-Status' => [function (BookingScenario $s) {
                $s->verwalter->forceFill(['is_active' => false])->save();

                return "/admin/users/{$s->verwalter->id}/edit";
            }, 'archive_status_id', '2'],
            'Raum' => [$archived('room', '/admin/rooms/{id}/edit'), 'archive_status_id', '2'],
            'Checkpunkt' => [$archived('checkpoint', '/admin/checkpoints/{id}/edit'), 'archive_status_id', '2'],
            'FAQ' => [$archived('faq', '/admin/faqs/{id}/edit'), 'archive_status_id', '2'],
            'FAQ-Kapitel' => [$archived('faqChapter', '/admin/faq_chapters/{id}/edit'), 'archive_status_id', '2'],
            'Person' => [$archived('person', '/admin/people/{id}/edit'), 'archive_status_id', '2'],
            'Bewertung' => [$archived('testimonial', '/admin/testimonials/{id}/edit'), 'archive_status_id', '2'],
            'Preisliste' => [$archived('pricelistPosition', '/admin/positions/{id}/edit'), 'archive_status_id', '2'],
        ];
    }

    #[DataProvider('forms')]
    public function test_select_preselects_saved_value(Closure $prepare, string $field, string $value): void
    {
        $s = BookingScenario::create();
        $uri = $prepare($s);

        $html = $this->actingAs($s->user('admin'))->get($uri)->assertOk()->getContent();

        preg_match('/<select name="'.$field.'".*?<\/select>/s', $html, $select);
        $this->assertNotEmpty($select, "Kein Auswahlfeld $field auf $uri");
        $this->assertMatchesRegularExpression('/<option value="'.$value.'"\s+selected/', $select[0], "$field: Wert $value nicht vorgewählt");
        $this->assertSame(1, substr_count($select[0], 'selected="selected"'), "$field: genau eine Option vorgewählt");
    }
}
