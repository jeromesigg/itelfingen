<?php

use App\Helper\HealthCheck;
use Illuminate\Support\Facades\Schedule;

Schedule::command('task:daily')
    ->dailyAt('04:00')
    ->onSuccess(fn () => HealthCheck::ping('job_daily'))
    ->onFailure(fn () => HealthCheck::ping('job_daily', fail: true));

Schedule::command('task:daily-evening')
    ->dailyAt('19:00')
    ->onSuccess(fn () => HealthCheck::ping('job_daily_evening'))
    ->onFailure(fn () => HealthCheck::ping('job_daily_evening', fail: true));

Schedule::command('task:weekly')
    ->weeklyOn(0, '00:00') // 0 = Sonntag
    ->onSuccess(fn () => HealthCheck::ping('job_weekly'))
    ->onFailure(fn () => HealthCheck::ping('job_weekly', fail: true));

Schedule::command('task:monthly')
    ->monthlyOn(23, '02:00')
    ->onSuccess(fn () => HealthCheck::ping('job_monthly'))
    ->onFailure(fn () => HealthCheck::ping('job_monthly', fail: true));

// Hintergrund-Aufträge (Push-Meldungen), aktiv erst mit QUEUE_CONNECTION=database; bei sync gibt es nichts abzuarbeiten.
// Kein Dauerprozess nötig: der Cron startet schedule:run jede Minute, der Worker leert die Warteschlange und beendet sich.
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=3')
    ->everyMinute()
    ->withoutOverlapping();
