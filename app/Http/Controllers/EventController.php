<?php

namespace App\Http\Controllers;

use App\City;
use App\Event;
use Validator;
use Carbon\Carbon;
use App\Helper\Helper;
use App\PricelistPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EventController extends Controller
{ 
    
    public function create(Request $request)
    {  
        $validator = Validator::make($request->all(), [
            'zipcode' => 'numeric',
            'name' => 'required',
            'total_person' => 'gt:0',
            'terms' => 'accepted',
        ], [
            'zipcode.numeric' => 'Die Postleitzahl muss numerisch sein.',
            'terms.accepted' => 'Die Hausordnung muss akzeptiert werden.',
            'total_person.gt' => 'Für die Buchung braucht es mindestens 1 Person.']);

        if ($validator->fails()) {
            return redirect()->to(url()->previous() . '#booking')
                        ->withErrors($validator, 'event')
                        ->withInput();
        }

        $input = $request->all();

        array_unshift($input['positions'],1);
        array_push($input['positions'],0);

        $input['plz'] = $input['zipcode'];
        $input['group_name'] = $input['group'];
        $input['event_status_id'] = config('status.event_neu');
        $input['contract_status_id'] = config('status.contract_offen');
        $name = $input['firstname'] . ' ' . $input['name'];
        $email = $input['email'];
        $input['start_date'] = new Carbon($input['start_date']);
        $input['end_date'] = new Carbon($input['end_date']);
        $event = Event::create($input); 
        $plpositions = PricelistPosition::where([['archive_status_id', config('status.aktiv')], ['bexio_code','<',300]])->orderby('bexio_code')->get();
        foreach($plpositions as $index => $plposition){
            Helper::CreateRePos($input['positions'][$index], $plposition['id'], $event);
        }

        Mail::send('emails.send_event',  $input, function($message) use($email, $name){
            $message
                ->to($email, $name)
                ->replyto(config('mail.from.address'), config('mail.from.name'))
                ->subject('Ihre Buchung für das Ferienhaus Itelfingen');
        });
        Mail::send('emails.send_event',  $input, function($message){
          $message->to(config('mail.from.address'), config('mail.from.name'))->subject('Buchung für das Ferienhaus Itelfingen');
        });
        return redirect()->to(url()->previous() . '#booking')->with('success_event', 'Vielen Dank für die Buchung. Wir werden uns so schnell wie möglich melden.');
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
