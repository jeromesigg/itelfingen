<?php

namespace App\Listeners;

use App\Events\EventInvoiceCreate;
use App\Helper\Helper;
use App\Services\BexioApiService;
use Carbon\Carbon;

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
     * @param  EventInvoiceCreate  $event
     * @return void
     */
    public function handle(EventInvoiceCreate $eventInvoice)
    {
        //
        $event = $eventInvoice->event;
        // Fehler von Bexio wirft BexioApiService selbst (RequestException bzw. BexioException).
        $invoice = app(BexioApiService::class)->postAction('kb_offer/'.$event['bexio_offer_id'].'/invoice');
        $event->update(['bexio_invoice_id' => $invoice['id']]);

        if (isset($invoice['id'])) {
            $data = [
                'is_valid_from' => Carbon::create($event->end_date)->toDateString(),
                'is_valid_to' => Carbon::create($event->end_date)->addDays(30)->toDateString(),
                'api_reference' => $event['id'],
            ];
            app(BexioApiService::class)->post('kb_invoice/'.$invoice['id'], $data);
            Helper::eventToGoogleCalendar($event);
        }
    }
}
