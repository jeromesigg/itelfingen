<?php

namespace App\Notifications;

use App\Mail\ApplicationWanted;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventApplicationWantedNotification extends Notification
{
    use Queueable;

    public Event $event;

    public $additional_text;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Event $event, $additional_text)
    {
        //
        $this->event = $event;
        $this->additional_text = $additional_text;
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
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return new ApplicationWanted($this->event, $this->additional_text);
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
        $end_date = Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y');
        $start_date = Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y');

        return [
            //
            'action' => 'Genossenschafts-Infos versendet',
            'name' => $event['firstname'].' '.$event['name'],
            'date' => $start_date.' bis '.$end_date,
        ];
    }
}
