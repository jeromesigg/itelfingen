<?php

namespace App\View\Components\Forms;

/**
 * Wert eines Formularfelds nach einem Validierungsfehler (old()) oder sonst der Standardwert.
 *
 * Laravel kennt nur eine alte Eingabe pro Anfrage. Stehen mehrere Formulare auf einer Seite
 * (Buchung und Kontakt auf der Startseite, beide mit "name" und "email"), gehört sie nur dem
 * abgeschickten: Formulare mit scope senden "_form_scope" mit, ihre Felder lesen old() nur bei passendem scope.
 */
class OldInput
{
    public static function value(string $name, mixed $default, ?string $scope = null): mixed
    {
        if ($scope !== null && old('_form_scope') !== $scope) {
            return $default;
        }

        // positions[30] heisst in old() positions.30
        return old(str_replace(['[', ']'], ['.', ''], $name), $default);
    }
}
