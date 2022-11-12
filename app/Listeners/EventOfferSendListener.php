<?php

namespace App\Listeners;

use App\Events\EventOfferSend;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Ixudra\Curl\Facades\Curl;

class EventOfferSendListener
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
     * @param  \App\Events\EventOfferSend  $event
     * @return void
     */
    public function handle(EventOfferSend $eventOffer)
    {
        //
        $event = $eventOffer->event;
        $end_date = Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y');
        $start_date = Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y');
        $title = "Angebot vom " . $start_date . " bis " . $end_date;

        Curl::to('https://api.bexio.com/2.0/kb_offer/' . $event['bexio_offer_id'] . '/issue')
            ->withHeader('Accept: application/json')
            ->withBearer(config('app.bexio_token'))
            ->post();
        Curl::to('https://api.bexio.com/2.0/kb_offer/' . $event['bexio_offer_id'] . '/send')
            ->withHeader('Accept: application/json')
            ->withBearer(config('app.bexio_token'))
            ->withData(
                array(
                    'recipient_email' => config('mail.invoice_mail'),
                    'subject' => $title,
                    'message' => $event['firstname'] . ' ' . $event['name'] .': [Network Link]' ,
                    'mark_as_open' => true
                )
            )
            ->asJson(true)
            ->post();
    }
}
