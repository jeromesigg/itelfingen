<?php

namespace App\Notifications;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventCreated;

class EventCreatedNotification extends Notification
{
    use Queueable;

    public Event $event;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Event $event)
    {
        //
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return EventCreated|MailMessage
     */
    public function toMail($notifiable)
    {
        return (new EventCreated($this->event));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $event = $this->event;
        return [
            'datum_start' => Carbon::parse($event->start_date)->format('d.m.Y'),
            'datum_ende' => Carbon::parse($event->end_date)->format('d.m.Y'),
            'anzahl_uebernachtung' => $event->total_days,
            'name' =>  $event->firstname . ' '. $event->name,
            'e-mail' => $event->email,
            'gruppe' => $event->group_name,
            'strasse' => $event->street,
            'ort' => $event->plz . ' ' . $event->city,
            'telefon' => $event->telephone,
            'total' => $event->total_amount,
            'bemerkung' => $event->comment
        ];
    }
}
