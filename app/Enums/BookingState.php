<?php

namespace App\Enums;

/**
 * Status einer Buchung (Tabelle event_statuses, Spalte events.event_status_id).
 */
enum BookingState: int
{
    case Neu = 5;
    case Bestaetigt = 10;
    case Eigene = 20;
    case Storniert = 50;

    /** Belegt das Haus fest (Kalender "occupied", Verfügbarkeit). */
    public function isOccupied(): bool
    {
        return $this === self::Bestaetigt || $this === self::Eigene;
    }

    /**
     * Alle Status ausser storniert (früher: event_status_id < event_storniert).
     *
     * @return list<self>
     */
    public static function notCancelled(): array
    {
        return [self::Neu, self::Bestaetigt, self::Eigene];
    }
}
