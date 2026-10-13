<?php

namespace App\Enums;

/**
 * Rollen der Benutzer (Tabelle roles, Spalte users.role_id). Rechte siehe tests/README.md "Berechtigungen".
 */
enum UserRole: int
{
    case Administrator = 1;
    case Team = 2;
    case Gast = 3;
    case Verwalter = 5;

    /** Inhalte und Benutzerverwaltung (Flag roles.is_admin) */
    public function isAdmin(): bool
    {
        return $this === self::Administrator;
    }

    /** Zugang zum Admin-Bereich überhaupt (Flag roles.is_team oder is_admin) */
    public function hasAdminArea(): bool
    {
        return in_array($this, [self::Administrator, self::Team, self::Verwalter], true);
    }
}
