<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use NotificationChannels\WebPush\Events\NotificationFailed;

/**
 * Vom Push-Dienst (Google, Apple, Mozilla) abgelehnte Push-Meldungen protokollieren.
 * Das Paket meldet Ablehnungen nur als Event; ohne diesen Listener bliebe der Grund unsichtbar.
 */
class LogFailedPushNotification
{
    public function handle(NotificationFailed $event): void
    {
        Log::warning('Push-Meldung abgelehnt', [
            'user_id' => $event->subscription->subscribable_id,
            'push_service' => parse_url($event->report->getEndpoint(), PHP_URL_HOST),
            'status' => $event->report->getResponse()?->getStatusCode(),
            'reason' => $event->report->getReason(),
            'response' => $event->report->getResponseContent(),
            'expired' => $event->report->isSubscriptionExpired(),
        ]);
    }
}
