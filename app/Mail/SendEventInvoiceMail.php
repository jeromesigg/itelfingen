<?php

namespace App\Mail;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
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
        $event = $this->event;

        $name = $event['firstname'] . ' ' . $event['name'];
        $start_date = Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y');
        $end_date = Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y');

        $invoice = Curl::to('https://api.bexio.com/2.0/kb_invoice/' . $event['bexio_invoice_id'])
            ->withHeader('Accept: application/json')
            ->withBearer(config('app.bexio_token'))
            ->get();
        $invoice = json_decode($invoice, true);

        $invoice_pdf = Curl::to('https://api.bexio.com/2.0/kb_invoice/' . $event['bexio_invoice_id'] . '/pdf')
            ->withHeader('Accept: application/json')
            ->withBearer(config('app.bexio_token'))
            ->asJson(true)
            ->get();

        return $this->markdown('emails.events.invoices', ['event' => $event, 'link' => $invoice['network_link']])
            ->to($event['email'], $name)
            ->bcc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Deine Rechnung zur Buchung vom ' . $start_date . ' bis ' . $end_date . ' im Ferienhaus Itelfingen')
            ->attachData(base64_decode($invoice_pdf['content']), 'Rechnung.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
