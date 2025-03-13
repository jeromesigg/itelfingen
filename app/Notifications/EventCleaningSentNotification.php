<?php

namespace App\Notifications;

use App\Mail\CleaningSent;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class EventCleaningSentNotification extends Notification
{
    use Queueable;

    public Event $event;

    public string $email;

    public string $text;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Event $event, $email, $text)
    {
        //
        $this->event = $event;
        $this->email = $email;
        $this->text = $text;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return new CleaningSent($this->event, $this->email, $this->text);
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
        $total_amount = $event->total_amount;
        $total_people = $event->total_people;
        $total_days = $event->total_days;

        return [
            //
            'action' => 'Reinigungsmail versendet',
            'name' => $event['firstname'].' '.$event['name'],
            'date' => $start_date.' bis '.$end_date,
            'days' => $total_days.' Nächte',
            'total_people' => $total_people,
            'total_amount' => $total_amount,
        ];
    }
}
