<?php

namespace App\Listeners;

use App\Events\EventCreated;
use App\Models\Position;
use App\Models\PricelistPosition;

class EventCreatedListener
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
     * @param  \App\Events\EventCreatedListener  $event
     * @return void
     */
    public function handle(EventCreated $event)
    {
        //
        $keys = array_keys($event->position_array);
        $positions = [];

        if ($event->one_day) {
            $positions[count($positions)] = ['bexio_code' => 20, 'amount' => 0.5];
            $positions[count($positions)] = ['bexio_code' => 50, 'amount' => 1];
        } else {
            $positions[count($positions)] = ['bexio_code' => 20, 'amount' => 1];
            foreach ($keys as $index => $key) {
                if ($key != 20) {
                    $positions[count($positions)] = ['bexio_code' => $key, 'amount' => $event->position_array[$key]];
                }
            }
        }
        $positions[count($positions)] = ['bexio_code' => 210, 'amount' => 0];

        foreach ($positions as $index => $position) {
            $plposition = PricelistPosition::where('bexio_code', '=', $position['bexio_code'])->first();
            $position['id'] = $plposition['id'];
            $position['name'] = $plposition['name'];
            $positions[$index] = $position;
        }

        foreach ($positions as $position) {
            $new_position = Position::FirstOrCreate([
                'event_id' => $event->event['id'],
                'pricelist_position_id' => $position['id'],
            ]);

            $new_position->update(['amount' => $position['amount']]);
        }
    }
}
