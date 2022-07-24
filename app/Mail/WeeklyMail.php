<?php

namespace App\Mail;

use App\Models\Contact;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class WeeklyMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The event instance.
     *
     * @var Collection
     */
    protected Collection $contacts;
    /**
     * The event instance.
     *
     * @var Collection
     */
    protected Collection $events;
    /**
     * Create a new message instance.
     *
     * @param Collection $events
     * @param Collection $contacts
     * @return void
     */
    public function __construct(Collection $events, Collection $contacts)
    {
        $this->events = $events;
        $this->contacts = $contacts;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.events.weekly', ['events' => $this->events, 'contacts' => $this->contacts])
            ->to(config('mail.from.address'), config('mail.from.name'))
            ->subject("Wöchentliches Erinnerungsmail");
    }
}
