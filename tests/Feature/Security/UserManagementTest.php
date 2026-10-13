<?php

namespace Tests\Feature\Security;

use App\Enums\ArchiveState;
use App\Enums\UserRole;
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
            'role_id' => UserRole::Administrator->value,
            'is_active' => 1,
            'archive_status_id' => ArchiveState::Aktiv->value,
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
            'role_id' => UserRole::Administrator->value,
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
            'role_id' => UserRole::Verwalter->value,
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
            'role_id' => UserRole::Verwalter->value,
            'signature' => UploadedFile::fake()->image('unterschrift.png'),
        ])->assertRedirect('/admin/users');

        $user = User::where('username', 'mit_unterschrift')->sole();
        Storage::disk('local')->assertExists($user->signature);
        // Ordner nach ID, nicht nach Benutzername (der Name landete sonst ungeprüft im Pfad)
        $this->assertStringStartsWith("signature/{$user->id}/", $user->signature);
    }

    public function test_duplicate_username_is_rejected_without_server_error(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('admin'))->from('/admin/users')->post('/admin/users', [
            'username' => 'team', 'password' => 'passwort123', 'role_id' => UserRole::Team->value,
        ])->assertRedirect('/admin/users')->assertSessionHasErrors('username');
        $this->actingAs($s->user('admin'))->from("/admin/users/{$s->gast->id}/edit")->patch("/admin/users/{$s->gast->id}", [
            'username' => 'team',
        ])->assertSessionHasErrors('username');

        $this->assertSame(1, User::where('username', 'team')->count());
    }

    public function test_own_username_can_stay_unchanged(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('team'))->patch("/admin/users/{$s->team->id}", [
            'username' => 'team', 'fullname' => 'Neu',
        ])->assertSessionHasNoErrors();
    }

    public function test_password_needs_ten_characters(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('admin'))->post('/admin/users', [
            'username' => 'kurz', 'password' => '1', 'role_id' => UserRole::Team->value,
        ])->assertSessionHasErrors('password');
        $this->actingAs($s->user('team'))->patch("/admin/users/{$s->team->id}", [
            'username' => 'team', 'password' => '123456789',
        ])->assertSessionHasErrors('password');

        $this->assertNull(User::where('username', 'kurz')->first());
        $this->assertTrue(Hash::check('password', $s->team->fresh()->password));
    }

    public static function usernames(): array
    {
        return [
            'Pfad nach oben' => ['../../app', false],
            'Schrägstrich' => ['team/admin', false],
            'Backslash' => ['team\\admin', false],
            'leer' => ['', false],
            'mit Leerzeichen und Umlaut' => ['Jürg Müller', true],
            'mit Punkt und Bindestrich' => ['j.muster-2', true],
        ];
    }

    #[DataProvider('usernames')]
    public function test_username_characters(string $username, bool $valid): void
    {
        $s = BookingScenario::create();

        $response = $this->actingAs($s->user('admin'))->post('/admin/users', [
            'username' => $username, 'password' => 'passwort123', 'role_id' => UserRole::Team->value,
        ]);

        $valid ? $response->assertSessionHasNoErrors() : $response->assertSessionHasErrors('username');
    }

    public function test_edit_form_does_not_require_password(): void
    {
        $s = BookingScenario::create();

        $html = $this->actingAs($s->user('team'))->get("/admin/users/{$s->team->id}/edit")->getContent();

        preg_match('/<input[^>]*name="password"[^>]*>/', $html, $field);
        $this->assertNotEmpty($field);
        $this->assertStringNotContainsString('required', $field[0]);
    }

    public function test_unknown_role_is_rejected(): void
    {
        $s = BookingScenario::create();

        // Mit dem Enum-Cast würde eine unbekannte Rollen-ID sonst einen Serverfehler auslösen.
        $this->actingAs($s->user('admin'))->from('/admin/users')->post('/admin/users', [
            'username' => 'falsche_rolle',
            'password' => 'passwort123',
            'role_id' => 4,
        ])->assertRedirect('/admin/users')->assertSessionHasErrors('role_id');
        $this->actingAs($s->user('admin'))->from("/admin/users/{$s->team->id}/edit")->patch("/admin/users/{$s->team->id}", [
            'username' => $s->team->username,
            'role_id' => 4,
        ])->assertSessionHasErrors('role_id');

        $this->assertNull(User::where('username', 'falsche_rolle')->first());
        $this->assertSame(UserRole::Team, $s->team->fresh()->role_id);
    }

    public function test_admin_deactivates_user(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('admin'))->patch("/admin/users/{$s->team->id}", [
            'username' => $s->team->username,
            'role_id' => $s->team->role_id->value,
            'archive_status_id' => ArchiveState::Archiviert->value,
        ])->assertRedirect('/admin/users');

        $this->assertFalse((bool) $s->team->fresh()->is_active);
    }

    public function test_admin_changes_role_of_other_user(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('admin'))->patch("/admin/users/{$s->team->id}", [
            'username' => $s->team->username,
            'role_id' => UserRole::Verwalter->value,
            'archive_status_id' => ArchiveState::Aktiv->value,
        ])->assertRedirect('/admin/users');

        $this->assertSame(UserRole::Verwalter, $s->team->fresh()->role_id);
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
