<?php

namespace App\Listeners;

use App\Events\EventOfferCreate;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
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
        if(is_null($event['bexio_user_id'])){
            $query = array(
                array(
                    'field' => 'name_1',
                    'value' => $event->name
                ),
                array(
                    'field' => 'name_2',
                    'value' => $event->firstname ? $event->firstname  : ''
                ),
                array(
                    'field' => 'address',
                    'value' => $event->street
                ),
                array(
                    'field' => 'postcode',
                    'value' => $event->plz
                ),);
            $person = Curl::to('https://api.bexio.com/2.0/contact/search')
                ->withHeader('Accept: application/json')
                ->withBearer(config('app.bexio_token'))
                ->withContentType('application/json')
                ->withData($query)
                ->asJson(true)
                ->post();

            if(count($person) === 0){
                $person = Curl::to('https://api.bexio.com/2.0/contact')
                    ->withHeader('Accept: application/json')
                    ->withBearer(config('app.bexio_token'))
                    ->withContentType('application/json')
                    ->withData( array(
                        'contact_type_id' => '2',
                        'name_1' => $event->name,
                        'name_2' => $event->firstname,
                        'address' => $event->street,
                        'postcode' => $event->plz,
                        'city' => $event->city,
                        'country_id' => 1,
                        'mail' => $event->email,
                        'phone_mobile' => $event->telephone,
                        'remarks' => $event->comment,
                        'user_id' => 1,
                        'owner_id' => 1,
                    ) )
                    ->asJson(true)
                    ->post();
            }
            else{
                $person = $person[0];
            }
            if(!isset($person->error)){
                $event->update(['bexio_user_id' => $person['id']]);
            }
        }

    }
}
