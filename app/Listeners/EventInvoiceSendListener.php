<?php

namespace App\Listeners;

use App\Events\EventInvoiceSend;
use App\Services\BexioApiService;
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

        $data =  ['is_valid_to' => now()->addDays(30)->toDateString(),];
        app(BexioApiService::class)->post('kb_invoice/'.$event['bexio_invoice_id'], $data);
        app(BexioApiService::class)->postAction('kb_invoice/'.$event['bexio_invoice_id'].'/issue');
        $data_send =  [
                    'recipient_email' => config('mail.invoice_mail'),
                    'subject' => $title,
                    'message' => $event['firstname'].' '.$event['name'].': [Network Link]',
                    'mark_as_open' => true,
                ];
        app(BexioApiService::class)->post('kb_invoice/'.$event['bexio_invoice_id'].'/send', $data_send);
    }
}
