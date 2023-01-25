<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Magarrent\LaravelCurrencyFormatter\Facades\Currency;

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

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Event  $event
     * @return void
     */
    public function __construct(Event $event, string $link, string $total)
    {
        $this->event = $event;
        $this->link = $link;
        $this->total = $total;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $event = $this->event;
        $name = $event['firstname'].' '.$event['name'];
        $PdfPath = public_path('files/Hausordnung.pdf');

        return $this->markdown('emails.events.offers', ['event' => $event, 'link' => $this->link, 'total' => Currency::currency('CHF')->format($this->total)])
            ->to($event['external'] ? config('mail.from.address') : $event['email'], $name)
            ->cc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Ihr Angebot zur Buchung ' . str_pad($this->event['id'],5,'0', STR_PAD_LEFT) . ' für das Ferienhaus Itelfingen')
            ->attach($PdfPath, [
                'as' => 'Hausordnung.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
