<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\BookingScenario;
use Tests\TestCase;

/**
 * Wartungsseite für Admins (admin/maintenance): offene Migrationen anzeigen und ausführen, Cache leeren.
 * Aktionen nur per POST, damit ein Link oder Bild auf einer fremden Seite nichts auslösen kann.
 */
class MaintenanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Vor jedem Aufruf faken: ein noch roter Test darf nie wirklich migrieren.
        Artisan::shouldReceive('call')->andReturn(0)->byDefault();
        Artisan::shouldReceive('output')->andReturn('')->byDefault();
    }

    public function test_admin_sees_maintenance_page_without_pending_migrations(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('admin'))->get('/admin/maintenance')
            ->assertOk()
            ->assertSee('Keine offenen Migrationen');
    }

    public function test_page_lists_pending_migrations(): void
    {
        $s = BookingScenario::create();
        DB::table('migrations')->where('migration', '2026_08_05_214942_create_bexio_tokens_table')->delete();

        $this->actingAs($s->user('admin'))->get('/admin/maintenance')
            ->assertOk()
            ->assertSee('2026_08_05_214942_create_bexio_tokens_table');
    }

    public function test_admin_runs_migrations(): void
    {
        $s = BookingScenario::create();
        Artisan::shouldReceive('call')->once()->with('migrate', ['--force' => true])->andReturn(0);
        Artisan::shouldReceive('output')->andReturn('Migrated: 2026_10_10_000000_beispiel');

        $this->actingAs($s->user('admin'))->post('/admin/maintenance/migrate')
            ->assertRedirect('/admin/maintenance')
            ->assertSessionHas('maintenance_output', 'Migrated: 2026_10_10_000000_beispiel');

        $this->actingAs($s->user('admin'))->get('/admin/maintenance')
            ->assertSee('Migrated: 2026_10_10_000000_beispiel');
    }

    public function test_admin_clears_cache(): void
    {
        $s = BookingScenario::create();
        Artisan::shouldReceive('call')->once()->with('optimize:clear')->andReturn(0);

        $this->actingAs($s->user('admin'))->post('/admin/maintenance/clear-cache')
            ->assertRedirect('/admin/maintenance');
    }

    public static function nonAdmins(): array
    {
        return ['verwalter' => ['verwalter'], 'team' => ['team'], 'gast' => ['gast']];
    }

    #[DataProvider('nonAdmins')]
    public function test_non_admins_cannot_use_maintenance(string $role): void
    {
        $s = BookingScenario::create();
        Artisan::shouldReceive('call')->never();

        $this->actingAs($s->user($role))->get('/admin/maintenance')->assertForbidden();
        $this->actingAs($s->user($role))->post('/admin/maintenance/migrate')->assertForbidden();
        $this->actingAs($s->user($role))->post('/admin/maintenance/clear-cache')->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        Artisan::shouldReceive('call')->never();

        $this->post('/admin/maintenance/migrate')->assertRedirect('/login');
    }

    public function test_actions_are_not_available_via_get(): void
    {
        $s = BookingScenario::create();
        Artisan::shouldReceive('call')->never();

        $this->actingAs($s->user('admin'))->get('/admin/maintenance/migrate')->assertMethodNotAllowed();
        $this->actingAs($s->user('admin'))->get('/admin/maintenance/clear-cache')->assertMethodNotAllowed();
    }

    public function test_admin_menu_links_maintenance_page(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('admin'))->get('/admin')->assertSee(route('admin.maintenance'));
        $this->actingAs($s->user('verwalter'))->get('/admin')->assertDontSee('/admin/maintenance');
    }
}
