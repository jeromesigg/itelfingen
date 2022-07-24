<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The event instance.
     *
     * @var \App\Models\Event
     */
    protected $event;
    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Event  $event
     * @return void
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $event = $this->event;
        $name = $event['firstname'] . ' ' . $event['name'];


        return $this->markdown('emails.events.created', ['event' => $event])
            ->to($event['email'], $name)
            ->bcc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Ihre Buchung für das Ferienhaus Itelfingen');
    }
}
