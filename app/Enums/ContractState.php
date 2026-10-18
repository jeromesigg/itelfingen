<?php

namespace App\Enums;

/**
 * Vertragsstatus einer Buchung (Tabelle contract_statuses, Spalte events.contract_status_id).
 * Die Werte sind aufsteigend nach Fortschritt: Offen → Angebot → Rechnung → Storniert.
 */
enum ContractState: int
{
    case Offen = 5;
    case AngebotErstellt = 7;
    case AngebotVersendet = 10;
    case RechnungErstellt = 20;
    case RechnungVersendet = 30;
    case Storniert = 50;

    /** Mindestens so weit fortgeschritten wie $state (Storniert gilt als am weitesten). */
    public function isAtLeast(self $state): bool
    {
        return $this->value >= $state->value;
    }

    /** Noch nicht so weit wie $state. */
    public function isBefore(self $state): bool
    {
        return $this->value < $state->value;
    }

    /**
     * Status vor $state, für Abfragen (früher: contract_status_id < ...).
     *
     * @return list<self>
     */
    public static function before(self $state): array
    {
        return array_values(array_filter(self::cases(), fn (self $case) => $case->isBefore($state)));
    }
}
