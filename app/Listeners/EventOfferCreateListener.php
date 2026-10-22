<?php

namespace App\Listeners;

use App\Enums\ContractState;
use App\Events\EventOfferCreate;
use App\Models\Position;
use App\Models\PricelistPosition;
use App\Services\BexioApiService;
use Carbon\Carbon;

class EventOfferCreateListener
{
    private const PARKING_CODE = 210;

    private const PARKING_INCLUDED = 3;

    /**
     * Menge einer Position in der Offerte: Hausmiete (20) und Zusatzkosten (> 200) fix,
     * Personen/Nutzung (21-200) pro Nacht, Parkplätze pro Tag, wobei drei im Preis inbegriffen sind.
     */
    public static function offerAmount(int $bexioCode, int|float $amount, int $nights): int|float
    {
        if ($bexioCode === self::PARKING_CODE) {
            return max($amount - self::PARKING_INCLUDED, 0) * $nights;
        }
        if ($bexioCode > 20 && $bexioCode <= 200) {
            return $amount * $nights;
        }

        return $amount;
    }

    /**
     * Rabatt der Buchung gilt ab Code 100, nicht aber für die Pauschale Frühbezug/Spätabgabe.
     */
    public static function discountApplies(int $bexioCode): bool
    {
        return $bexioCode >= 100 && $bexioCode !== PricelistPosition::CHECKIN_CHECKOUT_CODE;
    }

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
                $amount = self::offerAmount($position->pricelist_position['bexio_code'], $position['amount'], $event['total_days']);
                if ($amount > 0) {
                    $positions_array[] = [
                        'amount' => $amount,
                        'type' => 'KbPositionArticle',
                        'tax_id' => 16,
                        'article_id' => $position->pricelist_position['bexio_id'],
                        'unit_price' => $position->pricelist_position['price'],
                        'discount_in_percent' => self::discountApplies($position->pricelist_position['bexio_code']) ? $event['discount'] : 0,
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
            // Fehler von Bexio wirft BexioApiService selbst (RequestException bzw. BexioException).
            $offer = app(BexioApiService::class)->post('kb_offer', $data);
            $event->update([
                'bexio_offer_id' => $offer['id'],
                'contract_status_id' => ContractState::AngebotErstellt,
            ]);
        }
    }
}
