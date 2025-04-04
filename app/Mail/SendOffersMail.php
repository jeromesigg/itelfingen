<?php

namespace App\Mail;

use NumberFormatter;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOffersMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The event instance.
     *
     * @var \App\Models\Event
     */
    protected $event;

    protected $link;

    protected $total;

    protected $additional_text;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Event $event, string $link, string $total, string $additional_text)
    {
        $this->event = $event;
        $this->link = $link;
        $this->total = $total;
        $this->additional_text = $additional_text;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        set_time_limit(60); // Increase the execution time to 60 seconds

        $event = $this->event;
        $name = $event['firstname'].' '.$event['name'];
        $PdfPath = public_path('files/Hausordnung.pdf');
        $number = str_pad($this->event['id'], 5, '0', STR_PAD_LEFT);
        if (isset($event['foreign_key'])) {
            $number .= ' ('.$event['foreign_key'].')';
        }

        $fmt = new NumberFormatter( 'de_DE', NumberFormatter::CURRENCY );
        $total = $fmt->formatCurrency($this->total, "CHF");
        return $this->markdown('emails.events.offers', ['event' => $event, 'link' => $this->link, 'total' => $total, 'additional_text' => $this->additional_text])
            ->to($event['email'], $name)
            ->cc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Ihr Angebot zur Buchung '.$number.' für das Ferienhaus Itelfingen')
            ->attach($PdfPath, [
                'as' => 'Hausordnung.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
