<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;     
use Illuminate\Support\Facades\App;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

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
        DB::statement("SET lc_time_names = 'de_CH'");

        RateLimiter::for('booking-login', function (Request $request) {
            return [
                // Max 5 Versuche pro Minute pro IP
                Limit::perMinute(5)->by($request->ip()),
                // Zusätzlich: Max 3 Versuche pro Stunde pro Buchungsnummer
                // (verhindert, dass eine einzelne Buchung von vielen IPs aus attackiert wird)
                Limit::perHour(3)->by('booking:' . $request->input('id')),
            ];
        });
    }
}
