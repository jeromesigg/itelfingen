<?php

namespace App\Mail;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendOffersMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The event instance.
     *
     * @var Event
     */
    protected $event;

    protected $link;

    protected $total;

    protected $additionalText;

    /** Gültig bis (Bexio-Feld is_valid_until, Y-m-d) */
    protected ?string $validUntil;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Event $event, string $link, string $total, string $additional_text, ?string $valid_until = null)
    {
        $this->event = $event;
        $this->link = $link;
        $this->total = $total;
        $this->additionalText = $additional_text;
        $this->validUntil = $valid_until;
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
        $pdfPath = public_path('files/Hausordnung.pdf');
        $number = str_pad($this->event['id'], 5, '0', STR_PAD_LEFT);
        if (isset($event['foreign_key'])) {
            $number .= ' ('.$event['foreign_key'].')';
        }

        $total = 'CHF '.number_format((float) $this->total, 2, '.', '’');
        // Frist aus dem Bexio-Angebot, damit sie auch bei einer Erinnerung stimmt
        $valid_until = Carbon::parse($this->validUntil ?? now()->addMonth())->format('d.m.Y');

        return $this->markdown('emails.events.offers', ['event' => $event, 'link' => $this->link, 'total' => $total, 'valid_until' => $valid_until, 'additional_text' => $this->additionalText])
            ->to($event['email'], $name)
            ->cc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Dein Angebot zur Buchung '.$number.' für das Ferienhaus Itelfingen')
            ->attach($pdfPath, [
                'as' => 'Hausordnung.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
