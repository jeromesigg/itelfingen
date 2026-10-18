<?php

namespace App\Mail;

use App\Models\Event;
use Carbon\Carbon;
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
        if (isset($this->event['foreign_key'])) {
            $number .= ' ('.$this->event['foreign_key'].')';
        }

        // Meist am Vorabend versendet; bei kurzfristigen Buchungen auch am Abreisetag oder danach
        $end = Carbon::parse($this->event['end_date']);
        $heading = match (true) {
            $end->isTomorrow() => 'Morgen ist dein Abreisetag',
            $end->isToday() => 'Heute ist dein Abreisetag',
            default => 'Danke für deinen Aufenthalt',
        };

        return $this->markdown('emails.events.feedback', ['event' => $this->event, 'heading' => $heading])
            ->to($email, $name)
            ->cc(config('mail.from.address'), config('mail.from.name'))
            ->subject($heading.' – Infos zur Hausrückgabe (Buchung '.$number.')')
            ->attach($PdfPath, [
                'as' => 'Infos_vor_Buchung.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
