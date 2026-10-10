<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Benutzerverwaltung: Admin verwaltet alle, Team und Verwalter nur das eigene Profil ohne Rolle und Status.
 */
class UserManagementTest extends TestCase
{
    public static function nonAdmins(): array
    {
        return ['verwalter' => ['verwalter'], 'team' => ['team']];
    }

    #[DataProvider('nonAdmins')]
    public function test_non_admin_can_edit_own_profile(string $role): void
    {
        $s = BookingScenario::create();
        $user = $s->user($role);

        $this->actingAs($user)->patch("/admin/users/{$user->id}", [
            'username' => $user->username,
            'fullname' => 'Neuer Name',
            'phone' => '079 000 00 00',
            'password' => 'neuespasswort',
        ])->assertRedirect('/admin/users');

        $user->refresh();
        $this->assertSame('Neuer Name', $user->fullname);
        $this->assertTrue(Hash::check('neuespasswort', $user->password));
    }

    #[DataProvider('nonAdmins')]
    public function test_non_admin_cannot_change_own_role_or_status(string $role): void
    {
        $s = BookingScenario::create();
        $user = $s->user($role);
        $roleId = $user->role_id;

        $this->actingAs($user)->patch("/admin/users/{$user->id}", [
            'username' => $user->username,
            'role_id' => config('status.role_Administrator'),
            'is_active' => 1,
            'archive_status_id' => config('status.aktiv'),
        ]);

        $this->assertSame($roleId, $user->fresh()->role_id);
    }

    #[DataProvider('nonAdmins')]
    public function test_non_admin_cannot_change_admin_password(string $role): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user($role))->patch('/admin/users/1', [
            'username' => 'Administrator',
            'password' => 'gehackt123',
        ])->assertForbidden();

        $this->assertFalse(Hash::check('gehackt123', User::find(1)->password));
    }

    #[DataProvider('nonAdmins')]
    public function test_non_admin_cannot_create_or_delete_users(string $role): void
    {
        $s = BookingScenario::create();
        $count = User::count();

        $this->actingAs($s->user($role))->post('/admin/users', [
            'username' => 'eindringling',
            'password' => 'passwort123',
            'role_id' => config('status.role_Administrator'),
        ])->assertForbidden();
        $this->actingAs($s->user($role))->delete("/admin/users/{$s->admin->id}")->assertForbidden();

        $this->assertSame($count, User::count());
    }

    #[DataProvider('nonAdmins')]
    public function test_non_admin_cannot_download_foreign_signature(string $role): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user($role))->get("/admin/users/download/{$s->admin->id}")->assertForbidden();
    }

    public function test_admin_creates_user_with_role(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('admin'))->post('/admin/users', [
            'username' => 'neue_verwalterin',
            'fullname' => 'Neue Verwalterin',
            'password' => 'passwort123',
            'role_id' => config('status.role_Verwalter'),
        ])->assertRedirect('/admin/users');

        $user = User::where('username', 'neue_verwalterin')->sole();
        $this->assertTrue($user->isManager());
        $this->assertTrue(Hash::check('passwort123', $user->password));
    }

    public function test_admin_create_form_posts_to_store_route(): void
    {
        $s = BookingScenario::create();

        // Das Formular schickte bisher an users.create (GET-Route) und lief in einen 405.
        $this->actingAs($s->user('admin'))->get('/admin/users')
            ->assertOk()
            ->assertSee('action="'.route('users.store').'"', false);
    }

    public function test_admin_creates_user_with_signature(): void
    {
        Storage::fake('local');
        $s = BookingScenario::create();

        $this->actingAs($s->user('admin'))->post('/admin/users', [
            'username' => 'mit_unterschrift',
            'password' => 'passwort123',
            'role_id' => config('status.role_Verwalter'),
            'signature' => UploadedFile::fake()->image('unterschrift.png'),
        ])->assertRedirect('/admin/users');

        $user = User::where('username', 'mit_unterschrift')->sole();
        Storage::disk('local')->assertExists($user->signature);
    }

    public function test_admin_deactivates_user(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('admin'))->patch("/admin/users/{$s->team->id}", [
            'username' => $s->team->username,
            'role_id' => $s->team->role_id,
            'archive_status_id' => config('status.archiviert'),
        ])->assertRedirect('/admin/users');

        $this->assertFalse((bool) $s->team->fresh()->is_active);
    }

    public function test_admin_changes_role_of_other_user(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('admin'))->patch("/admin/users/{$s->team->id}", [
            'username' => $s->team->username,
            'role_id' => config('status.role_Verwalter'),
            'archive_status_id' => config('status.aktiv'),
        ])->assertRedirect('/admin/users');

        $this->assertSame(config('status.role_Verwalter'), $s->team->fresh()->role_id);
        $this->assertTrue((bool) $s->team->fresh()->is_active);
    }

    public function test_admin_deletes_user(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('admin'))->delete("/admin/users/{$s->team->id}")->assertRedirect('/admin/users');

        $this->assertNull($s->team->fresh());
    }

    public function test_empty_password_keeps_old_password(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('team'))->patch("/admin/users/{$s->team->id}", [
            'username' => $s->team->username,
            'password' => '',
        ]);

        $this->assertTrue(Hash::check('password', $s->team->fresh()->password));
    }
}
