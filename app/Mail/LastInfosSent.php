<?php

namespace App\Mail;

use App\Helper\Helper;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        $email = $this->event['email'];
        $name = $this->event['firstname'] . ' ' . $this->event['name'];
        $outputFile = Helper::PrintParking($this->event);
        return $this->markdown('emails.events.last_infos', ['event' => $this->event])
            ->to($email, $name)
            ->bcc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Deine Buchung für das Ferienhaus Itelfingen')
            ->attach($PdfPath, [
                'as'    => 'Infos_vor_Buchung.pdf',
                'mime'   => 'application/pdf',
            ])
            ->attach($outputFile, [
                'as'    => 'Parkkarte.pdf',
                'mime'   => 'application/pdf',
            ]);
    }
}
