<?php

namespace App\Listeners;

use App\Events\ApplicationCreatedEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Ixudra\Curl\Facades\Curl;

class ApplicationCreatedListener
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
     * @param  \App\Events\ApplicationCreatedEvent  $event
     * @return void
     */
    public function handle(ApplicationCreatedEvent $event)
    {
        //
        $application = $event->application;
        if(is_null($application['bexio_user_id'])){
            $query = array(
                array(
                    'field' => 'name_1',
                    'value' => $application->name
                ),
                array(
                    'field' => 'name_2',
                    'value' => $application->firstname ?: ''
                ),
                array(
                    'field' => 'address',
                    'value' => $application->street
                ),
                array(
                    'field' => 'postcode',
                    'value' => $application->plz
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
                        'name_1' => $application->name,
                        'name_2' => $application->firstname,
                        'address' => $application->street,
                        'postcode' => $application->plz,
                        'city' => $application->city,
                        'country_id' => 1,
                        'mail' => $application->email,
                        'phone_mobile' => $application->telephone,
                        'remarks' => $application->comment,
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
                $application->update(['bexio_user_id' => $person['id']]);
            }
        }
    }
}
