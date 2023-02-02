<?php

namespace App\Notifications;

use App\Mail\SendOffersMail;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Ixudra\Curl\Facades\Curl;

class EventOfferSendNotification extends Notification
{
    use Queueable;

    public Event $event;
    public $additional_text;

    public array $offer;

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
        $offer = Curl::to('https://api.bexio.com/2.0/kb_offer/'.$event['bexio_offer_id'])
            ->withHeader('Accept: application/json')
            ->withBearer(config('app.bexio_token'))
            ->get();
        $offer = json_decode($offer, true);
        $this->offer = $offer;
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
        return new SendOffersMail($this->event, $this->offer['network_link'], $this->offer['total'], $this->additional_text);
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
            'action' => 'Angebot versendet',
        ];
    }
}
