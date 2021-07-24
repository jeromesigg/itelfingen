<?php

namespace App\Http\Controllers;

use App\City;
use App\Event;
use Illuminate\Http\Request;
use App\Http\Requests\EventRequest;
use Illuminate\Support\Facades\Mail;

use Validator;

class EventController extends Controller
{ 
    
    public function create(EventRequest $request)
    {  
        $validator = Validator::make($request->all(), [
            'zipcode' => 'numeric',
            'name' => 'required',
            'terms' => 'accepted',
        ], [
            'zipcode.numeric' => 'Die Postleitzahl muss numerisch sein.',
            'terms.accepted' => 'Die Hausordnung muss akzeptiert werden.']);

        if ($validator->fails()) {
            return redirect()->to(url()->previous() . '#booking')
                        ->withErrors($validator)
                        ->withInput();
        }

        $input = $request->all();
        $input['other_adults'] = max($input['other_adults'], 0);
        $input['member_adults'] = max($input['member_adults'], 0);
        $input['other_kids'] = max($input['other_kids'], 0);
        $input['member_kids'] = max($input['member_kids'], 0);
        $input['total_people'] = $input['other_adults'] + $input['member_adults'] + $input['other_kids'] + $input['member_kids'];
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
