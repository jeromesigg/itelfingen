<?php

namespace Tests\Feature\Security;

use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Wartungs-Routen mit Basic-Auth (Middleware auth.deploy): /deploy migriert die Datenbank,
 * /bexio/connect verbindet das Bexio-Konto.
 */
class DeployRouteTest extends TestCase
{
    /** @var array<string, string|false> */
    private array $originalEnv = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Vor jedem Aufruf faken: ein noch roter Test darf nie wirklich migrieren.
        Artisan::shouldReceive('call')->andReturn(0)->byDefault();
    }

    public static function protectedRoutes(): array
    {
        return ['deploy' => ['/deploy'], 'bexio connect' => ['/bexio/connect']];
    }

    #[DataProvider('protectedRoutes')]
    public function test_requires_credentials(string $uri): void
    {
        Artisan::shouldReceive('call')->never();

        $this->get($uri)->assertUnauthorized();
        $this->withBasicAuth('deploy', 'falsch')->get($uri)->assertUnauthorized();
    }

    #[DataProvider('protectedRoutes')]
    public function test_stays_closed_when_credentials_are_not_configured(string $uri): void
    {
        Artisan::shouldReceive('call')->never();
        // So sieht es aus, wenn DEPLOY_USER/DEPLOY_PASS fehlen oder die Config gecacht ist (env() liefert dann null).
        $this->withoutDeployEnv();
        config(['services.deploy.user' => null, 'services.deploy.password' => null]);

        $this->get($uri)->assertUnauthorized();
        $this->withBasicAuth('', '')->get($uri)->assertUnauthorized();
    }

    public function test_deploy_runs_migrations_with_valid_credentials(): void
    {
        Artisan::shouldReceive('call')->once()->with('migrate', ['--force' => true]);
        Artisan::shouldReceive('call')->once()->with('optimize:clear');

        $this->withBasicAuth('deploy', 'deploy-secret')->get('/deploy')
            ->assertOk()
            ->assertJson(['status' => 'ok']);
    }

    public function test_bexio_connect_redirects_to_bexio_with_valid_credentials(): void
    {
        $response = $this->withBasicAuth('deploy', 'deploy-secret')->get('/bexio/connect');

        $response->assertRedirect();
        $this->assertStringStartsWith('https://auth.bexio.com/', $response->headers->get('Location'));
    }

    protected function tearDown(): void
    {
        // putenv() gilt für den ganzen Prozess, also auch für die folgenden Tests.
        foreach ($this->originalEnv as $name => $value) {
            putenv("$name=$value");
            $_ENV[$name] = $_SERVER[$name] = $value;
        }

        parent::tearDown();
    }

    private function withoutDeployEnv(): void
    {
        foreach (['DEPLOY_USER', 'DEPLOY_PASS'] as $name) {
            $this->originalEnv[$name] = getenv($name);
            putenv($name);
            unset($_ENV[$name], $_SERVER[$name]);
        }
    }
}
