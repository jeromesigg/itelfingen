<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class FeedbackMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The event instance.
     *
     * @var Event
     */
    protected $event;

    /**
     * Create a new message instance.
     *
     * @param  Event  $event
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
        $email = $this->event['email'];
        $name = $this->event['firstname'].' '.$this->event['name'];

        return $this->markdown('emails.events.feedback', ['event' => $this->event])
            ->to($email, $name)
            ->cc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Dein Feedback zur Buchung ' . str_pad($this->event['id'],5,'0', STR_PAD_LEFT) . ' für das Ferienhaus Itelfingen');
    }
}
