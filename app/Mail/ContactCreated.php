<?php

namespace App\Mail;

use App\Models\Contact;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The event instance.
     *
     * @var \App\Models\Contact
     */
    protected $contact;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Contact  $contact
     * @return void
     */
    public function __construct(Contact $contact)
    {
        $this->contact = $contact;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.contacts.created', ['contact' => $this->contact])
            ->to($this->contact['email'], $this->contact['name'])
            ->bcc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Kopie deiner Nachricht ans Ferien- und Lagerhaus Itelfingen');
    }
}
