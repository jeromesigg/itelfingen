<?php

namespace App\Listeners;

use App\Events\EventInvoiceCreate;
use App\Helper\Helper;
use App\Services\BexioApiService;
use Carbon\Carbon;
use Ixudra\Curl\Facades\Curl;

class EventInvoiceCreateListener
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
     * @param  \App\Events\EventInvoiceCreate  $event
     * @return void
     */
    public function handle(EventInvoiceCreate $eventInvoice)
    {
        //
        $event = $eventInvoice->event;
        $invoice = app(BexioApiService::class)->postAction('kb_offer/'.$event['bexio_offer_id'].'/invoice');
        if (! isset($invoice['error_code'])) {
            $event->update([
                'bexio_invoice_id' => $invoice['id'], ]);
        } else {
            abort($invoice['error_code'], $invoice['message']);
        }

        if (isset($invoice['id'])) {
            $data = [
                'is_valid_from' => Carbon::create($event->end_date)->toDateString(),
                'is_valid_to' => Carbon::create($event->end_date)->addDays(30)->toDateString(),
                'api_reference' => $event['id'],
            ];
            $response = app(BexioApiService::class)->post('kb_invoice/'.$invoice['id'], $data);
            Helper::EventToGoogleCalendar($event);
        }
    }
}
