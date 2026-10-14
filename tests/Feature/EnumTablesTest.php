<?php

namespace Tests\Feature;

use App\Enums\ArchiveState;
use App\Enums\BookingState;
use App\Enums\ContractState;
use App\Enums\UserRole;
use App\Models\ArchiveStatus;
use App\Models\ContractStatus;
use App\Models\EventStatus;
use App\Models\Role;
use Tests\TestCase;

/**
 * Enum-Werte entsprechen genau den Zeilen der Status-Tabellen (BasisdatenSeeder = Prod-Stammdaten).
 */
class EnumTablesTest extends TestCase
{
    public function test_enums_match_status_tables(): void
    {
        $values = fn (string $enum) => array_column($enum::cases(), 'value');

        $this->assertEqualsCanonicalizing($values(ArchiveState::class), ArchiveStatus::pluck('id')->all());
        $this->assertEqualsCanonicalizing($values(BookingState::class), EventStatus::pluck('id')->all());
        $this->assertEqualsCanonicalizing($values(ContractState::class), ContractStatus::pluck('id')->all());
        $this->assertEqualsCanonicalizing($values(UserRole::class), Role::pluck('id')->all());
    }

    public function test_role_rights_match_role_flags(): void
    {
        foreach (Role::all() as $role) {
            $enum = UserRole::from($role->id);
            $this->assertSame((bool) $role->is_admin, $enum->isAdmin(), "is_admin von {$role->name}");
            $this->assertSame((bool) ($role->is_team || $role->is_admin), $enum->hasAdminArea(), "is_team von {$role->name}");
        }
    }
}
