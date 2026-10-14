<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Events\ConnectionEstablished;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        Paginator::useBootstrap();
        if (class_exists('Swift_Preferences')) {
            \Swift_Preferences::getInstance()->setTempDir(storage_path().'/tmp');
        } else {
        }
        // Deutsche Monatsnamen in DATE_FORMAT (Auslastungs-Graphen). Erst beim Verbindungsaufbau setzen,
        // sonst bräuchte jeder App-Start eine Datenbank (composer install, Docker-Build, CI).
        Event::listen(function (ConnectionEstablished $event) {
            if ($event->connection->getDriverName() === 'mysql') {
                $event->connection->statement("SET lc_time_names = 'de_CH'");
            }
        });

        // Öffentliche Formulare (Buchung, Kontakt, Bewerbung) lösen Mails, Slack und Einträge aus.
        RateLimiter::for('public-forms', fn (Request $request) => [
            Limit::perMinute(5)->by('form-minute:'.$request->ip()),
            Limit::perHour(20)->by('form-hour:'.$request->ip()),
        ]);

        RateLimiter::for('booking-login', function (Request $request) {
            return [
                // Max 5 Versuche pro Minute pro IP
                Limit::perMinute(5)->by($request->ip()),
                // Zusätzlich: Max 3 Versuche pro Stunde pro Buchungsnummer
                // (verhindert, dass eine einzelne Buchung von vielen IPs aus attackiert wird)
                Limit::perHour(3)->by('booking:'.$request->input('id')),
            ];
        });
    }
}
