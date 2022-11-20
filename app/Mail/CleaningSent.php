<?php

namespace App\Mail;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CleaningSent extends Mailable
{
    use Queueable, SerializesModels;


    protected $event;
    protected $email;
    protected $text;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Event $event, $email, $text)
    {
        $this->event = $event;
        $this->email = $email;
        $this->text = $text;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.events.cleaning', ['text' => $this->text])
            ->to($this->email)
            ->cc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Reinigungsanfrage '. str_pad($this->event['id'],5,'0', STR_PAD_LEFT) . ' für das Ferienhaus Itelfingen vom '. Carbon::parse($this->event->end_date)->format('d.m.Y'));
    }
}
