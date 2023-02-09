<?php

namespace App\Notifications;

use App\Mail\SendEventConfirmation;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use jeremykenedy\Slack\Laravel\Facade as Slack;

class EventInvoiceCreatedNotification extends Notification
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
        return ['database', 'slack', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return SendEventConfirmation
     */
    public function toMail($notifiable)
    {
        return new SendEventConfirmation($this->event, $this->additional_text);
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        $event = $this->event;
        $end_date = Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y');
        $start_date = Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y');

//        if (config('app.env') == 'production') {
            return (new SlackMessage)
                ->from(config('slack.username'), config('slack.icon'))
                ->to(config('slack.channel'))
                ->content('Es hat eine neue Buchung gegeben. Vom '.$start_date.' bis '.$end_date.' Von '.$event['firstname'].' '.$event['name'].' - '.$event['group_name']);
//        }
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
            'action' => 'Rechnung erstellt',
        ];
    }
}
