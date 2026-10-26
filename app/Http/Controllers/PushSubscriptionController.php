<?php

namespace App\Http\Controllers;

use App\Notifications\AdminPushNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use NotificationChannels\WebPush\PushSubscription;

/**
 * Push-Meldungen auf dem aktuellen Gerät ein- und ausschalten (Schalter im Profil, resources/js/admin/push-toggle.js).
 * Ein Gerät ist über den Endpoint des Browsers erkennbar; jeder Benutzer verwaltet nur seine eigenen Geräte.
 */
class PushSubscriptionController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'url', 'starts_with:https://', 'max:'.PushSubscription::ENDPOINT_MAX_LENGTH],
            'keys.p256dh' => ['required', 'string', 'max:255'],
            'keys.auth' => ['required', 'string', 'max:255'],
            'contentEncoding' => ['nullable', 'in:aesgcm,aes128gcm'],
        ]);

        $request->user()->updatePushSubscription(
            $validated['endpoint'],
            $validated['keys']['p256dh'],
            $validated['keys']['auth'],
            $validated['contentEncoding'] ?? 'aes128gcm',
        );

        return response()->json(['subscribed' => true], 201);
    }

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate(['endpoint' => ['required', 'string']]);

        $request->user()->deletePushSubscription($validated['endpoint']);

        return response()->json(['subscribed' => false]);
    }

    /**
     * Testmeldung an alle eigenen Geräte, sofort und ohne Queue: zeigt, ob VAPID-Schlüssel und Gerät stimmen.
     */
    public function test(Request $request): JsonResponse
    {
        $request->user()->notifyNow(new AdminPushNotification('Testmeldung', 'Push-Meldungen funktionieren auf diesem Gerät.', url('/admin')));

        return response()->json(['sent' => true]);
    }
}
