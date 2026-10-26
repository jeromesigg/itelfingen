<?php

namespace App\Http\Controllers;

use App\Notifications\AdminPushNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\AnonymousNotifiable;
use NotificationChannels\WebPush\PushSubscription;
use NotificationChannels\WebPush\WebPushChannel;
use Throwable;

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
     * Testmeldung an das Gerät, auf dem der Knopf gedrückt wurde, sofort und ohne Queue.
     * Gibt die Antwort des Push-Dienstes zurück, damit Fehler (z. B. falsche VAPID-Schlüssel) direkt sichtbar sind.
     */
    public function test(Request $request): JsonResponse
    {
        $validated = $request->validate(['endpoint' => ['required', 'string']]);
        $subscription = $request->user()->pushSubscriptions()->where('endpoint', $validated['endpoint'])->first();
        if (! $subscription) {
            return response()->json(['message' => 'Dieses Gerät ist auf dem Server nicht angemeldet. Bitte «Ausschalten» und wieder «Einschalten».'], 422);
        }

        $notification = new AdminPushNotification('Testmeldung', 'Push-Meldungen funktionieren auf diesem Gerät.', url('/admin'));
        try {
            // Erst hier auflösen: ungültige VAPID-Schlüssel scheitern schon beim Erzeugen des Kanals
            [$report] = app(WebPushChannel::class)->send((new AnonymousNotifiable)->route('WebPush', collect([$subscription])), $notification);
        } catch (Throwable $exception) {
            report($exception);

            return response()->json(['message' => 'Versand nicht möglich: '.$exception->getMessage()], 500);
        }

        if (! $report->isSuccess()) {
            $status = $report->getResponse()?->getStatusCode();

            return response()->json(['message' => 'Der Push-Dienst hat abgelehnt'.($status ? " ($status)" : '').': '.$report->getReason()], 502);
        }

        return response()->json(['message' => 'Vom Push-Dienst angenommen. Erscheint keine Meldung, sind Mitteilungen auf dem Gerät ausgeschaltet.']);
    }
}
