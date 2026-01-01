<?php

namespace App\Listeners;

use App\Events\EventOfferCreate;
use Ixudra\Curl\Facades\Curl;

class EventContactCreateListener
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
     * @param  \App\Events\EventOfferCreate  $event
     * @return void
     */
    public function handle(EventOfferCreate $eventOffer)
    {
        //
        $event = $eventOffer->event;
        if (is_null($event['bexio_user_id'])) {
            $query = [
                [
                    'field' => 'name_1',
                    'value' => $event->name,
                ],
                [
                    'field' => 'name_2',
                    'value' => $event->firstname ? $event->firstname : '',
                ],
                [
                    'field' => 'address',
                    'value' => $event->street . ' ' . $event->house_number,
                ],
                [
                    'field' => 'postcode',
                    'value' => $event->plz,
                ], ];
            $person = Curl::to('https://api.bexio.com/2.0/contact/search')
                ->withHeader('Accept: application/json')
                ->withBearer(config('app.bexio_token'))
                ->withContentType('application/json')
                ->withData($query)
                ->asJson(true)
                ->post();

            if (count($person) === 0) {
                $person = Curl::to('https://api.bexio.com/2.0/contact')
                    ->withHeader('Accept: application/json')
                    ->withBearer(config('app.bexio_token'))
                    ->withContentType('application/json')
                    ->withData([
                        'contact_type_id' => '2',
                        'name_1' => $event->name,
                        'name_2' => $event->firstname,
                        'street_name' => $event->street,
                        'house_number' => $event->house_number ?: '',
                        'postcode' => $event->plz,
                        'city' => $event->city,
                        'country_id' => 1,
                        'mail' => $event->email,
                        'phone_mobile' => $event->telephone,
                        'remarks' => $event->comment,
                        'user_id' => 1,
                        'owner_id' => 1,
                    ])
                    ->asJson(true)
                    ->post();
            } else {
                $person = $person[0];
            }
            if (! isset($person->errors)) {
                $event->update(['bexio_user_id' => $person['id']]);
            }
        }
    }
}
