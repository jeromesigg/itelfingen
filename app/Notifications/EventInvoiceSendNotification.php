<?php

namespace App\Notifications;

use App\Mail\SendEventInvoiceMail;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Ixudra\Curl\Facades\Curl;

class EventInvoiceSendNotification extends Notification
{
    use Queueable;

    public Event $event;
    public $additional_text;

    public array $invoice;

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
        $invoice = Curl::to('https://api.bexio.com/2.0/kb_invoice/'.$event['bexio_invoice_id'])
            ->withHeader('Accept: application/json')
            ->withBearer(config('app.bexio_token'))
            ->get();
        $invoice = json_decode($invoice, true);
        $this->invoice = $invoice;
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
        return new SendEventInvoiceMail($this->event, $this->invoice, $this->additional_text);
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
            'action' => 'Rechnung versendet',
        ];
    }
}
