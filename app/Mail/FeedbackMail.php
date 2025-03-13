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
        $PdfPath = storage_path('app/contracts/Infos_vor_Buchung.pdf');
        $number = str_pad($this->event['id'], 5, '0', STR_PAD_LEFT);
        if (isset($event['foreign_key'])) {
            $number .= ' ('.$event['foreign_key'].')';
        }

        return $this->markdown('emails.events.feedback', ['event' => $this->event])
            ->to($email, $name)
            ->cc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Dein Feedback zur Buchung '.$number.' für das Ferienhaus Itelfingen')
            ->attach($PdfPath, [
                'as' => 'Infos_vor_Buchung.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
