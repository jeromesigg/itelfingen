<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CleaningSent extends Mailable
{
    use Queueable, SerializesModels;

    protected $email;

    protected $text;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email, $text)
    {
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
            ->subject('Reinigungsanfrage Ferienhaus Itelfingen');
    }
}
