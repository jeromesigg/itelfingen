<?php

namespace App\Listeners;

use App\Events\ApplicationCreatedEvent;
use App\Services\BexioApiService;
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
     * @return void
     */
    public function handle(ApplicationCreatedEvent $event)
    {
        //
        $application = $event->application;
        if (is_null($application['bexio_user_id'])) {
            $query = [
                [
                    'field' => 'name_1',
                    'value' => $application->name,
                ],
                [
                    'field' => 'name_2',
                    'value' => $application->firstname ?: '',
                ],
                [
                    'field' => 'address',
                    'value' => $application->street . ' ' . $application->house_number,
                ],
                [
                    'field' => 'postcode',
                    'value' => $application->plz,
                ], ];
            $person = app(BexioApiService::class)->post('contact/search', $query);

            if (count($person) === 0) {
                $data = [
                        'contact_type_id' => '2',
                        'name_1' => $application->name,
                        'name_2' => $application->firstname,
                        'street_name' => $application->street,
                        'house_number' => $application->house_number ?: '',
                        'postcode' => $application->plz,
                        'city' => $application->city,
                        'country_id' => 1,
                        'mail' => $application->email,
                        'phone_mobile' => $application->telephone,
                        'remarks' => $application->comment,
                        'user_id' => 1,
                        'owner_id' => 1,
                    ];
                $person = app(BexioApiService::class)->post('contact', $data);
            } else {
                $person = $person[0];
            }
            if (! isset($person->errors)) {
                $application->update(['bexio_user_id' => $person['id']]);
            }
        }
    }
}
