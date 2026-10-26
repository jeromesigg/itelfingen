<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

/**
 * Push-Meldung auf die Geräte von Verwaltern und Admins (versendet über App\Services\AdminPush).
 * Tipp auf die Meldung öffnet $url im Admin-Bereich (Service Worker public/admin-sw.js).
 * Mit QUEUE_CONNECTION=sync läuft der Versand sofort, mit einem Queue-Worker im Hintergrund.
 */
class AdminPushNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /** Wie lange der Push-Dienst die Meldung für ein ausgeschaltetes Handy aufbewahrt (Sekunden). */
    private const TIME_TO_LIVE = 86400;

    public function __construct(public string $title, public string $body, public string $url) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->title)
            ->body($this->body)
            ->icon('/img/pwa/icon-192.png')
            ->badge('/img/pwa/badge-96.png')
            ->lang('de-CH')
            ->data(['url' => $this->url])
            ->options(['TTL' => self::TIME_TO_LIVE, 'urgency' => 'high']);
    }
}
