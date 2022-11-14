<?php

namespace App\Listeners;

use App\Events\EventInvoiceSend;
use Carbon\Carbon;
use Ixudra\Curl\Facades\Curl;

class EventInvoiceSendListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\EventInvoiceSend  $event
     * @return void
     */
    public function handle(EventInvoiceSend $eventInvoice)
    {
        //
        $event = $eventInvoice->event;
        $end_date = Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y');
        $start_date = Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y');
        $title = 'Rechnung vom '.$start_date.' bis '.$end_date;

        Curl::to('https://api.bexio.com/2.0/kb_invoice/'.$event['bexio_invoice_id'])
            ->withHeader('Accept: application/json')
            ->withHeader('Content-Type: application/json')
            ->withBearer(config('app.bexio_token'))
            ->withData(
                [
                    'is_valid_to' => now()->addDays(30)->toDateString(),
                ]
            )
            ->asJson(true)
            ->post();

        Curl::to('https://api.bexio.com/2.0/kb_invoice/'.$event['bexio_invoice_id'].'/issue')
            ->withHeader('Accept: application/json')
            ->withBearer(config('app.bexio_token'))
            ->post();

        Curl::to('https://api.bexio.com/2.0/kb_invoice/'.$event['bexio_invoice_id'].'/send')
            ->withHeader('Accept: application/json')
            ->withBearer(config('app.bexio_token'))
            ->withData(
                [
                    'recipient_email' => config('mail.invoice_mail'),
                    'subject' => $title,
                    'message' => $event['firstname'].' '.$event['name'].': [Network Link]',
                    'mark_as_open' => true,
                ]
            )
            ->asJson(true)
            ->post();
    }
}
