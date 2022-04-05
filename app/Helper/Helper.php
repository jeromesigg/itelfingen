<?php

namespace App\Helper;

use App\Models\Event;
use App\Models\Position;
use App\Models\PricelistPosition;

class Helper
{
    static function CreateRePos($positions, Event $event){
        foreach ($positions as $position) {
            $new_position = Position::FirstOrCreate([
                'event_id' => $event['id'],
                'pricelist_position_id' => $position['id']
            ]);

            $new_position->update(['amount' => $position['amount']]);
        }
    }

}
