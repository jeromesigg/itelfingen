<?php

namespace App\Helper;

use App\Event;
use App\Position;
use Carbon\Carbon;
use INSAN\ICS\ICS;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class Helper
{
    static function CreateRePos($amount, $id, Event $event){
        $position = Position::FirstOrCreate([
            'event_id' => $event['id'],
            'pricelist_position_id' => $id
        ]);
        $position->update(['amount' => $amount,]);
    }
    
}
