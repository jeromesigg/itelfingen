<?php

namespace App\Mail;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Ixudra\Curl\Facades\Curl;

class SendEventInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The event instance.
     *
     * @var \App\Models\Event
     */
    protected $event;

    protected $invoice;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Event  $event
     * @return void
     */
    public function __construct(Event $event, $invoice)
    {
        $this->event = $event;
        $this->invoice = $invoice;
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
        $start_date = Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y');
        $end_date = Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y');

        $invoice_pdf = Curl::to('https://api.bexio.com/2.0/kb_invoice/'.$event['bexio_invoice_id'].'/pdf')
            ->withHeader('Accept: application/json')
            ->withBearer(config('app.bexio_token'))
            ->asJson(true)
            ->get();

        return $this->markdown('emails.events.invoices', ['event' => $event, 'link' => $this->invoice['network_link']])
            ->to($event['external'] ? config('mail.from.address') : $event['email'], $name)
            ->cc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Deine Rechnung zur Buchung ' . str_pad($this->event['id'],5,'0', STR_PAD_LEFT) . ' vom '.$start_date.' bis '.$end_date.' im Ferienhaus Itelfingen')
            ->attachData(base64_decode($invoice_pdf['content']), 'Rechnung.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
