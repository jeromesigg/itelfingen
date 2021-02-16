<?php

namespace App\Http\Controllers;

use App\Event;
use App\Homepage;
use Carbon\Carbon;
use Redirect,Response;
use Illuminate\Http\Request;
use App\Http\Requests\EventRequest;
use Illuminate\Support\Facades\Mail;

class EventController extends Controller
{ 
    public function create(EventRequest $request)
    {  
        $input = $request->all();
        $input['event_status_id'] = config('status.event_neu');
        $input['contract_status_id'] = config('status.contract_offen');
        $name = $input['firstname'] . ' ' . $input['name'];
        $email = $input['email'];
        Event::create($input);
        Mail::send('emails.send_event',  $input, function($message) use($email, $name){
            $message->to($email, $name)->subject('Ihre Buchung für das Ferienhaus Itelfingen');
        });
        Mail::send('emails.send_event',  $input, function($message){
          $message->to('jerome.sigg@gmail.com', 'Jerome')->subject('Buchung für das Ferienhaus Itelfingen');
        });
        return redirect()->to(url()->previous() . '#booking')->with('success_event', 'Vielen Dank für die Nachricht. Wir werden uns so schnell wie möglich melden.');
    }
}
