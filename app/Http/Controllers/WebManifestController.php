<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

/**
 * Web-App-Manifest: macht den Admin-Bereich auf dem Handy installierbar.
 * Browser laden das Manifest ohne Login-Cookie, deshalb gleich für alle Rollen.
 */
class WebManifestController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'id' => '/admin',
            'name' => config('app.name'),
            'short_name' => 'Itelfingen',
            'lang' => 'de-CH',
            'start_url' => '/admin',
            // Ganze Seite, damit Login und Logout nicht aus der App hinausführen
            'scope' => '/',
            'display' => 'standalone',
            'background_color' => '#ffffff',
            'theme_color' => '#A5BAA5',
            'icons' => [
                ['src' => '/img/pwa/icon-192.png', 'sizes' => '192x192', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => '/img/pwa/icon-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'any'],
                ['src' => '/img/pwa/icon-maskable-512.png', 'sizes' => '512x512', 'type' => 'image/png', 'purpose' => 'maskable'],
            ],
            // Nur Seiten, die jede Admin-Rolle öffnen darf (Anfragen sieht das Team nicht)
            'shortcuts' => [
                ['name' => 'Buchungen', 'url' => '/admin/events', 'icons' => [['src' => '/img/pwa/icon-192.png', 'sizes' => '192x192']]],
            ],
        ], options: JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            ->header('Content-Type', 'application/manifest+json');
    }
}
