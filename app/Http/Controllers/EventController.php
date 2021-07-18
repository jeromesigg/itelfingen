<?php

namespace App\Http\Controllers;

use App\City;
use DateTime;
use App\Event;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Requests\EventRequest;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{ 
    public function create(EventRequest $request)
    {  
        $validator = Validator::make($request->all(), [
            'zipcode' => 'numeric',
        ], ['zipcode.numeric' => 'Die Postleitzahl muss numerisch sein.']);

        if ($validator->fails()) {
            return redirect()->to(url()->previous() . '#booking')
                        ->withErrors($validator)
                        ->withInput();
        }

        $input = $request->all();
        $input['plz'] = $input['zipcode'];
        $input['group_name'] = $input['group'];
        $input['event_status_id'] = config('status.event_neu');
        $input['contract_status_id'] = config('status.contract_offen');
        $name = $input['firstname'] . ' ' . $input['name'];
        $email = $input['email'];
        Event::create($input); 

        Mail::send('emails.send_event',  $input, function($message) use($email, $name){
            $message
                ->to($email, $name)
                ->replyto(config('mail.from.address'), config('mail.from.name'))
                ->subject('Ihre Buchung für das Ferienhaus Itelfingen');
        });
        Mail::send('emails.send_event',  $input, function($message){
          $message->to(config('mail.from.address'), config('mail.from.name'))->subject('Buchung für das Ferienhaus Itelfingen');
        });
        return redirect()->to(url()->previous() . '#booking')->with('success_event', 'Vielen Dank für die Nachricht. Wir werden uns so schnell wie möglich melden.');
    }

    public function searchResponseCity(Request $request)
    {
        // $query = $request->get('term','');
        $cities = City::search($request->get('term'))->get();
        return $cities;
        foreach ($cities as $city) {
                $data[]=array('name'=>$city->name,'plz'=>$city->plz,'id'=>$city->id);
        }
        if(count($data))
             return $data;
        else
            return ['name'=>'','plz'=>'','id'=>''];
    }

}
