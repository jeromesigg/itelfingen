<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\Role;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\UnitTestCase;

/**
 * Rollen am Benutzer (Grundlage der Middleware role:team|verwalter|admin), ohne Datenbank.
 */
class UserRoleTest extends UnitTestCase
{
    private function user(?string $role, bool $active = true): User
    {
        $roles = [
            'admin' => ['id' => UserRole::Administrator, 'is_admin' => 1, 'is_team' => 0],
            'team' => ['id' => UserRole::Team, 'is_admin' => 0, 'is_team' => 1],
            'gast' => ['id' => UserRole::Gast, 'is_admin' => 0, 'is_team' => 0],
            'verwalter' => ['id' => UserRole::Verwalter, 'is_admin' => 0, 'is_team' => 1],
        ];
        $user = (new User)->forceFill(['role_id' => $role ? $roles[$role]['id'] : null, 'is_active' => $active ? 1 : 0]);

        return $user->setRelation('role', $role ? (new Role)->forceFill($roles[$role]) : null);
    }

    /**
     * @return array<string, array{?string, bool, array{bool, bool, bool}}> Rolle, aktiv, [team, verwalter, admin]
     */
    public static function levels(): array
    {
        return [
            'Admin' => ['admin', true, [true, true, true]],
            'Verwalter' => ['verwalter', true, [true, true, false]],
            'Team' => ['team', true, [true, false, false]],
            'Gast' => ['gast', true, [false, false, false]],
            'ohne Rolle (selbst registriert)' => [null, true, [false, false, false]],
            'Admin inaktiv' => ['admin', false, [false, false, false]],
            'Verwalter inaktiv' => ['verwalter', false, [false, false, false]],
            'Team inaktiv' => ['team', false, [false, false, false]],
        ];
    }

    #[DataProvider('levels')]
    public function test_role_levels(?string $role, bool $active, array $expected): void
    {
        $user = $this->user($role, $active);

        $this->assertSame($expected, [
            $user->hasRoleLevel('team'),
            $user->hasRoleLevel('verwalter'),
            $user->hasRoleLevel('admin'),
        ]);
    }

    public function test_manager_flags(): void
    {
        $this->assertTrue($this->user('verwalter')->isManager());
        $this->assertFalse($this->user('admin')->isManager());
        $this->assertTrue($this->user('admin')->canManageBookings());
        $this->assertFalse($this->user('team')->canManageBookings());
    }

    public function test_unknown_level_is_an_error(): void
    {
        // Tippfehler in routes/web.php (z. B. role:verwalten) sollen auffallen statt still durchzulassen.
        $this->expectException(\UnhandledMatchError::class);

        $this->user('admin')->hasRoleLevel('verwalten');
    }
}
