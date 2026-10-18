<?php

namespace Tests\Unit;

use App\Enums\BookingState;
use App\Enums\ContractState;
use App\Enums\UserRole;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Regeln der Enums. Dass die Werte zu den Tabellen passen, prüft Feature/EnumTablesTest.
 */
class EnumsTest extends TestCase
{
    public function test_contract_states_are_ordered_by_progress(): void
    {
        $this->assertSame(
            [ContractState::Offen, ContractState::AngebotErstellt, ContractState::AngebotVersendet, ContractState::RechnungErstellt, ContractState::RechnungVersendet, ContractState::Storniert],
            ContractState::cases()
        );
        $this->assertTrue(ContractState::RechnungVersendet->isAtLeast(ContractState::AngebotVersendet));
        $this->assertTrue(ContractState::AngebotVersendet->isAtLeast(ContractState::AngebotVersendet));
        $this->assertFalse(ContractState::AngebotErstellt->isAtLeast(ContractState::AngebotVersendet));
        $this->assertTrue(ContractState::Offen->isBefore(ContractState::AngebotErstellt));
        $this->assertSame([ContractState::Offen, ContractState::AngebotErstellt], ContractState::before(ContractState::AngebotVersendet));
        $this->assertNotContains(ContractState::Storniert, ContractState::before(ContractState::Storniert));
    }

    public static function bookingStates(): array
    {
        return [
            'neu' => [BookingState::Neu, false],
            'bestätigt' => [BookingState::Bestaetigt, true],
            'eigene Termine' => [BookingState::Eigene, true],
            'storniert' => [BookingState::Storniert, false],
        ];
    }

    #[DataProvider('bookingStates')]
    public function test_occupied_booking_states(BookingState $state, bool $occupied): void
    {
        $this->assertSame($occupied, $state->isOccupied());
        $this->assertSame($state !== BookingState::Storniert, in_array($state, BookingState::notCancelled(), true));
    }

    public static function roles(): array
    {
        return [
            'Administrator' => [UserRole::Administrator, true, true],
            'Verwalter' => [UserRole::Verwalter, false, true],
            'Team' => [UserRole::Team, false, true],
            'Gast' => [UserRole::Gast, false, false],
        ];
    }

    #[DataProvider('roles')]
    public function test_role_rights(UserRole $role, bool $admin, bool $adminArea): void
    {
        $this->assertSame([$admin, $adminArea], [$role->isAdmin(), $role->hasAdminArea()]);
    }
}
