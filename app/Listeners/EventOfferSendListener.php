<?php

namespace App\Listeners;

use App\Events\EventOfferSend;
use App\Services\BexioApiService;
use Carbon\Carbon;

class EventOfferSendListener
{
    /** Bexio kb_item_status_id eines noch nicht ausgestellten Angebots */
    private const OFFER_STATUS_DRAFT = 1;

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
     * @param  EventOfferSend  $event
     * @return void
     */
    public function handle(EventOfferSend $eventOffer)
    {
        //
        $event = $eventOffer->event;
        $end_date = Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y');
        $start_date = Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y');
        $title = 'Angebot vom '.$start_date.' bis '.$end_date;

        // Bei einer Erinnerung ist das Angebot bereits ausgestellt, ein zweites Ausstellen lehnt Bexio ab.
        $offer = app(BexioApiService::class)->get('kb_offer/'.$event['bexio_offer_id']);
        if (($offer['kb_item_status_id'] ?? null) === self::OFFER_STATUS_DRAFT) {
            app(BexioApiService::class)->post('kb_offer/'.$event['bexio_offer_id'].'/issue');
        }
        $data = [
            'recipient_email' => config('mail.invoice_mail'),
            'subject' => $title,
            'message' => $event['firstname'].' '.$event['name'].': [Network Link]',
            'mark_as_open' => true,
        ];
        app(BexioApiService::class)->post('kb_offer/'.$event['bexio_offer_id'].'/send', $data);
    }
}
