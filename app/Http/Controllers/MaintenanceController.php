<?php

namespace App\Http\Controllers;

use App\Models\BexioToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

/**
 * Wartungsseite für Admins: ersetzt Wartungs-URLs, weil es auf dem Hosting keinen SSH-Zugang gibt.
 * Aktionen nur per POST (mit CSRF-Schutz), damit ein Link oder Bild auf einer fremden Seite nichts auslöst.
 */
class MaintenanceController extends Controller
{
    public function index()
    {
        $title = 'Wartung';
        $pending = $this->pendingMigrations();
        $bexioToken = BexioToken::first();

        return view('admin.maintenance.index', compact('title', 'pending', 'bexioToken'));
    }

    /**
     * Bexio verbinden (OAuth). Der state verknüpft den Callback (routes/web.php, bexio.callback) mit dieser
     * Weiterleitung, sonst könnte jemand ein fremdes Bexio-Konto verbinden.
     */
    public function connectBexio(Request $request)
    {
        $state = Str::random(40);
        $request->session()->put('bexio_oauth_state', $state);

        $params = http_build_query([
            'client_id' => config('services.bexio.client_id'),
            'redirect_uri' => route('bexio.callback'),
            'response_type' => 'code',
            'state' => $state,
            'scope' => 'openid offline_access contact_edit kb_invoice_edit kb_offer_edit article_show article_show',
        ]);

        return redirect("https://auth.bexio.com/realms/bexio/protocol/openid-connect/auth?{$params}");
    }

    public function migrate()
    {
        Artisan::call('migrate', ['--force' => true]);

        return $this->backWithOutput();
    }

    public function clearCache()
    {
        Artisan::call('optimize:clear');

        return $this->backWithOutput();
    }

    private function backWithOutput()
    {
        return redirect()->route('admin.maintenance')->with('maintenance_output', Artisan::output());
    }

    /**
     * Migrationen, die auf dieser Datenbank noch nicht gelaufen sind.
     *
     * @return list<string>
     */
    private function pendingMigrations(): array
    {
        $migrator = app('migrator');
        $files = array_keys($migrator->getMigrationFiles(database_path('migrations')));
        $ran = $migrator->repositoryExists() ? $migrator->getRepository()->getRan() : [];

        return array_values(array_diff($files, $ran));
    }
}
