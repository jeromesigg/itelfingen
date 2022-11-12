<?php

namespace App\Listeners;

use App\Events\EventInvoiceCreate;
use App\Helper\Helper;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Ixudra\Curl\Facades\Curl;
use jeremykenedy\Slack\Laravel\Facade as Slack;

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
        $invoice = Curl::to('https://api.bexio.com/2.0/kb_offer/' . $event['bexio_offer_id'] . '/invoice')
            ->withHeader('Accept: application/json')
            ->withBearer(config('app.bexio_token'))
            ->post();

        $invoice = json_decode($invoice, true);
        if (!isset($invoice['error_code'])) {
            $event->update([
                'bexio_invoice_id' => $invoice['id']]);

        } else {
            abort($invoice['error_code'], $invoice['message']);
        }

        if (isset($invoice['id'])){
            $response = Curl::to('https://api.bexio.com/2.0/kb_invoice/' . $invoice['id'])
                ->withHeader('Accept: application/json')
                ->withHeader('Content-Type: application/json')
                ->withBearer(config('app.bexio_token'))
                ->withData(
                    array(
                        'is_valid_from' => Carbon::create($event->end_date)->toDateString(),
                        'is_valid_to' => Carbon::create($event->end_date)->addDays(30)->toDateString(),
                        'api_reference' => $event['id'],
                    )
                )
                ->asJson(true)
                ->post();

            if (config('app.env') == 'production') {
                 Helper::EventToGoogleCalendar($event);
            }
        }
    }
}
