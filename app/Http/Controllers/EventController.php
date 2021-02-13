<?php

namespace App\Http\Controllers;

use App\Event;
use App\Homepage;
use Redirect,Response;
use Illuminate\Http\Request;

class EventController extends Controller
{ 
    public function create(Request $request)
    {  
        $input = $request->all();
        $input['event_status_id'] = config('status.event_neu');
        $input['contract_status_id'] = config('status.contract_offen');
        Event::create($input);
        return redirect()->to(url()->previous() . '#booking')->with('success_event', 'Vielen Dank für die Nachricht. Wir werden uns so schnell wie möglich melden.');
    }
}
