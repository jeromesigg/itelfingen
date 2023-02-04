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
     * @param  \App\Models\Event  $event
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
        $PdfPath = storage_path('app/contracts/Infos_vor_Buchung.pdf');
        $PdfPath_HO = public_path('files/Hausordnung.pdf');
        $email = $this->event['email'];
        $name = $this->event['firstname'].' '.$this->event['name'];
        $outputFile = Helper::PrintParking($this->event);

        return $this->markdown('emails.events.last_infos', ['event' => $this->event])
            ->to($email, $name)
            ->cc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Deine Buchung ' . str_pad($this->event['id'],5,'0', STR_PAD_LEFT) . ' für das Ferienhaus Itelfingen')
            ->attach($PdfPath, [
                'as' => 'Infos_vor_Buchung.pdf',
                'mime' => 'application/pdf',
            ])
            ->attach($outputFile, [
                'as' => 'Parkkarte.pdf',
                'mime' => 'application/pdf',
            ])
            ->attach($PdfPath_HO, [
                'as' => 'Hausordnung.pdf',
                'mime' => 'application/pdf',
            ]);
    }
}