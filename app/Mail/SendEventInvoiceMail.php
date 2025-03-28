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
     */
    protected Event $event;

    protected $invoice;

    protected $additional_text;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Event $event, $invoice, $additional_text)
    {
        $this->event = $event;
        $this->invoice = $invoice;
        $this->additional_text = $additional_text;
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
        $number = str_pad($this->event['id'], 5, '0', STR_PAD_LEFT);
        if (isset($event['foreign_key'])) {
            $number .= ' ('.$event['foreign_key'].')';
        }

        $invoice_pdf = Curl::to('https://api.bexio.com/2.0/kb_invoice/'.$event['bexio_invoice_id'].'/pdf')
            ->withHeader('Accept: application/json')
            ->withBearer(config('app.bexio_token'))
            ->asJson(true)
            ->get();

        return $this->markdown('emails.events.invoices', ['event' => $event, 'link' => $this->invoice['network_link'], 'additional_text' => $this->additional_text])
            ->to($event['email'], $name)
            ->cc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Deine Rechnung zur Buchung '.$number.' vom '.$start_date.' bis '.$end_date.' im Ferienhaus Itelfingen')
            ->attachData(base64_decode($invoice_pdf['content']), 'Rechnung.pdf', [
                'mime' => 'application/pdf',
            ]);
    }
}
