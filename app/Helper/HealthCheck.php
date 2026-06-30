<?php

namespace App\Helper;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HealthCheck
{
    public static function ping(string $jobName, bool $fail = false): void
    {
        $url = config("services.healthchecks.{$jobName}");

        if (! $url) {
            return;
        }

        try {
            Http::timeout(5)->get($fail ? "{$url}/fail" : $url);
        } catch (\Throwable $e) {
            Log::warning("Healthcheck-Ping für {$jobName} fehlgeschlagen: " . $e->getMessage());
        }
    }
}