<?php

namespace App\Listeners;

use App\Events\EventOfferCreate;
use App\Models\Position;
use App\Services\BexioApiService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class EventOfferCreateListener
{
    private const PARKING_CODE = 210;

    private const PARKING_INCLUDED = 3;

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
                // Hausmiete (20) und Zusatzkosten (> 200) fix, Personen/Nutzung pro Nacht,
                // Parkplätze pro Tag, wobei drei im Preis inbegriffen sind.
                $code = $position->pricelist_position['bexio_code'];
                $amount = $position['amount'];
                if ($code == self::PARKING_CODE) {
                    $amount = max($position['amount'] - self::PARKING_INCLUDED, 0) * $event['total_days'];
                } elseif ($code > 20 && $code <= 200) {
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
            $data = [
                'title' => $title,
                'contact_id' => $event->bexio_user_id,
                'user_id' => 1,
                'is_valid_from' => now(),
                'is_valid_until' => now()->addMonth(),
                'api_reference' => $event['id'],
                'positions' => $positions_array,
            ];
            $offer = app(BexioApiService::class)->post('kb_offer', $data);

            if (! isset($offer['error_code'])) {
                $event->update([
                    'bexio_offer_id' => $offer['id'],
                    'contract_status_id' => config('status.contract_angebot_erstellt'),
                ]);
            } else {
                Log::channel('emergency')->error($offer);
            }
        }
    }
}
