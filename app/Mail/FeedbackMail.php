<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
     * @param Event $event
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
        $name = $this->event['firstname'] . ' ' . $this->event['name'];
        return $this->markdown('emails.events.feedback', ['event' => $this->event])
            ->to($email, $name)
            ->bcc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Dein Feedback für das Ferienhaus Itelfingen');
    }
}
