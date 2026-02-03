<?php

namespace App\Mail;

use App\Helper\Helper;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LastInfosSent extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The event instance.
     *
     * @var \App\Models\Event
     */
    protected $event;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Event $event, $additional_text)
    {
        $this->event = $event;
        $this->additional_text = $additional_text;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $PdfPath = storage_path('app/contracts/Infos_vor_Buchung.pdf');
        $email = $this->event['email'];
        $name = $this->event['firstname'].' '.$this->event['name'];
        $outputFile = Helper::PrintParking($this->event);
        $number = str_pad($this->event['id'], 5, '0', STR_PAD_LEFT);
        if (isset($event['foreign_key'])) {
            $number .= ' ('.$event['foreign_key'].')';
        }

        return $this->markdown('emails.events.last_infos', ['event' => $this->event, 'additional_text' => $this->additional_text])
            ->to($email, $name)
            ->cc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Die letzten Informationen zu deiner Buchung '.$number.' für das Ferienhaus Itelfingen')
            ->attach($PdfPath, [
                'as' => 'Infos_vor_Buchung.pdf',
                'mime' => 'application/pdf',
            ])
            ->attach($outputFile, [
                'as' => 'Parkkarte.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}
