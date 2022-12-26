<?php

namespace App\Listeners;

use App\Events\EventOfferCreate;
use App\Models\Position;
use Carbon\Carbon;
use Ixudra\Curl\Facades\Curl;

class EventOfferCreateListener
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
     * @param  object  $event
     * @return void
     */
    public function handle(EventOfferCreate $eventOffer)
    {
        //
        $event = $eventOffer->event;
        if (is_null($event['bexio_offer_id'])) {
            $end_date = Carbon::create($event['end_date'])->locale('de_CH')->format('d.m.Y');
            $start_date = Carbon::create($event['start_date'])->locale('de_CH')->format('d.m.Y');
            $title = 'Buchung vom '.$start_date.' bis '.$end_date;
            $positions = Position::with('pricelist_position')->where('event_id', $event['id'])->get()->sortBy('pricelist_position.bexio_code');
            $positions_array = [];
            foreach ($positions as $position) {
                $amount = $position['amount'];
                if ($position->pricelist_position['bexio_code'] > 200) {
                    $amount = max($position['amount'] - 3, 0) * $event['total_days'];
                } elseif ($position->pricelist_position['bexio_code'] == 20){
                    if( $event['total_days'] == 0) {
                        $amount = $position['amount'] / 2;
                    }
                } else {
                    $amount = $event['total_days'] * $position['amount'];
                }
                if ($amount > 0) {
                    $positions_array[] = [
                        'amount' => $amount,
                        'type' => 'KbPositionArticle',
                        'tax_id' => 16,
                        'article_id' => $position->pricelist_position['bexio_id'],
                        'unit_price' => $position->pricelist_position['price'],
                        'discount_in_percent' => $position->pricelist_position['bexio_code'] < 100 ? 0 : $event['discount'],
                    ];
                }
            }
            $offer = Curl::to('https://api.bexio.com/2.0/kb_offer')
                ->withHeader('Accept: application/json')
                ->withBearer(config('app.bexio_token'))
                ->withContentType('application/json')
                ->withData(
                    [
                        'title' => $title,
                        'contact_id' => $event->bexio_user_id,
                        'user_id' => 1,
                        'is_valid_from' => now(),
                        'is_valid_until' => Carbon::create($event->start_date)->addDays(-14),
                        'api_reference' => $event['id'],
                        'positions' => $positions_array,
                    ]
                )
                ->asJson(true)
                ->post();

            if (! isset($offer['error_code'])) {
                $event->update([
                    'bexio_offer_id' => $offer['id'],
                    'contract_status_id' => config('status.contract_angebot_erstellt'),
                ]);
            }
        }
    }
}
