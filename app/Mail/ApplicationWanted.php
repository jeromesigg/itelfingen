<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationWanted extends Mailable
{
    use Queueable, SerializesModels;

    protected $event;

    public $additional_text;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Event $event, $additional_text)
    {
        $this->event = $event;
        $this->additional_text = $additional_text;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $event = $this->event;
        $name = $event['firstname'].' '.$event['name'];

        return $this->markdown('emails.events.application', ['event' => $event, 'additional_text' => $this->additional_text])
            ->to($event['email'], $name)
            ->cc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Dein Interesse '.str_pad($event['id'], 5, '0', STR_PAD_LEFT).' für die Genossenschaft Ferienhaus Itelfingen');
    }
}
