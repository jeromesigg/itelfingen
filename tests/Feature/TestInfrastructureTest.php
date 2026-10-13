<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\Homepage;
use App\Models\PricelistPosition;
use App\Models\Role;
use App\Models\User;
use App\Providers\AppServiceProvider;
use App\Services\GoogleCalendarService;
use Illuminate\Database\Events\ConnectionEstablished;
use Illuminate\Database\MySqlConnection;
use Illuminate\Support\Facades\DB;
use Mockery;
use RuntimeException;
use Tests\Support\BookingScenario;
use Tests\Support\FakeGoogleCalendar;
use Tests\TestCase;

/**
 * Sichert ab, dass Test-DB, Seeder, Factories und ein Seitenaufruf über den ganzen Stack funktionieren.
 */
class TestInfrastructureTest extends TestCase
{
    public function test_runs_against_sqlite_in_memory(): void
    {
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
    }

    public function test_refuses_to_refresh_a_real_database(): void
    {
        // So sähe es bei gecachter Config aus: phpunit.xml greift nicht, die Verbindung zeigt auf MySQL.
        // Zurücksetzen, bevor RefreshDatabase beim Aufräumen die Standard-Verbindung benutzt.
        config(['database.default' => 'mysql']);

        try {
            $this->beforeRefreshingDatabase();
            $this->fail('beforeRefreshingDatabase() hätte abbrechen müssen.');
        } catch (RuntimeException $e) {
            $this->assertStringContainsString("'mysql'", $e->getMessage());
        } finally {
            config(['database.default' => 'sqlite']);
        }
    }

    public function test_booting_the_app_does_not_connect_to_the_database(): void
    {
        // composer install (package:discover), Docker-Build und CI starten die App ohne Datenbank.
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.host' => '127.0.0.1',
            'database.connections.mysql.port' => 1,
        ]);

        try {
            (new AppServiceProvider($this->app))->boot();
            $this->assertTrue(true);
        } finally {
            DB::purge('mysql');
            config(['database.default' => 'sqlite']);
        }
    }

    public function test_mysql_connection_gets_german_month_names(): void
    {
        $connection = Mockery::mock(MySqlConnection::class)->shouldIgnoreMissing();
        $connection->shouldReceive('getDriverName')->andReturn('mysql');
        $connection->shouldReceive('statement')->once()->with("SET lc_time_names = 'de_CH'");

        event(new ConnectionEstablished($connection));
    }

    public function test_google_calendar_is_faked(): void
    {
        $this->assertInstanceOf(FakeGoogleCalendar::class, app(GoogleCalendarService::class));
    }

    public function test_seeder_creates_master_data(): void
    {
        $this->assertSame(4, Role::count());
        $this->assertNotNull(Homepage::find(1));
        $this->assertTrue(User::find(1)->isAdmin());
        $this->assertSame([20, 30, 40, 50, 210, 220, 300], PricelistPosition::pluck('bexio_code')->all());
    }

    public function test_scenario_creates_users_with_expected_rights(): void
    {
        $s = BookingScenario::create();

        $this->assertTrue($s->admin->isAdmin());
        $this->assertTrue($s->verwalter->isManager());
        $this->assertTrue($s->team->isTeam());
        $this->assertFalse($s->team->isAdmin());
        $this->assertFalse($s->gast->isTeam());
        $this->assertNull($s->registered->role_id);
        $this->assertCount(4, $s->event->positions);
    }

    public function test_database_is_reset_between_tests(): void
    {
        // Der vorherige Test hat Buchungen angelegt, hier dürfen nur die Stammdaten da sein.
        $this->assertSame(0, Event::count());
        $this->assertSame(1, User::count());
    }

    public function test_home_page_renders(): void
    {
        BookingScenario::create();

        $this->get('/')->assertOk();
    }

    public function test_admin_dashboard_renders_for_admin(): void
    {
        $s = BookingScenario::create();

        $this->actingAs($s->user('admin'))->get('/admin')->assertOk();
    }
}
