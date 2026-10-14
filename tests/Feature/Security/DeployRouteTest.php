<?php

namespace Tests\Feature\Security;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

/**
 * Die frühere Wartungs-URL /deploy (GET, migrierte die Datenbank) ist ersetzt durch die Wartungsseite
 * admin/maintenance (nur Admin, nur POST). Siehe MaintenanceTest.
 */
class DeployRouteTest extends TestCase
{
    public function test_old_deploy_url_is_gone(): void
    {
        // Vor dem Aufruf faken: ein noch roter Test darf nie wirklich migrieren.
        Artisan::shouldReceive('call')->never();

        $this->get('/deploy')->assertNotFound();
        $this->withBasicAuth('deploy', 'deploy-secret')->get('/deploy')->assertNotFound();
    }

    public function test_old_bexio_connect_url_is_gone(): void
    {
        $this->get('/bexio/connect')->assertNotFound();
    }
}
