<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Mail\EventCreated;
use App\Models\City;
use App\Models\Event;
use App\Models\PricelistPosition;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Validator;

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

        $input['plz'] = $input['zipcode'];
        $input['group_name'] = $input['group'];
        $input['event_status_id'] = config('status.event_neu');
        $input['contract_status_id'] = config('status.contract_offen');
        $name = $input['firstname'] . ' ' . $input['name'];
        $email = $input['email'];
        $one_day = false;
        if ($input['total_days']<1){
            $input['start_date'] = new Carbon($input['date']);
            $input['end_date'] = new Carbon($input['date']);
            $input['total_days'] = 1;
            $one_day = true;
        }
        else {
            $input['start_date'] = new Carbon($input['start_date']);
            $input['end_date'] = new Carbon($input['end_date']);
        }
        $event = Event::create($input);
        $keys = array_keys($input['positions']);
        $positions = [];

        if ($one_day){
            $positions[sizeof($positions)] = ['bexio_code' => 20, 'amount' => 0.5];
            $positions[sizeof($positions)] = ['bexio_code' => 50, 'amount' => 1];

        }
        else {
            $positions[sizeof($positions)] = ['bexio_code' => 20, 'amount' => 1];
            foreach ($keys  as $index => $key)
            {
                $positions[sizeof($positions)] = ['bexio_code' => $key, 'amount' => $input['positions'][$key]];
            }
        }
        $positions[sizeof($positions)] = ['bexio_code' => 210, 'amount' => 0];

        foreach ($positions as $index => $position) {
            $plposition = PricelistPosition::where('bexio_code','=', $position['bexio_code'])->first();
            $position['id'] = $plposition['id'];
            $position['name'] = $plposition['name'];
            $positions[$index] = $position;
        }
        Helper::CreateRePos($positions, $event);

//        return (new EventCreated($event));
        Mail::send(new EventCreated($event));

        return redirect()->to(url()->previous() . '#booking')->with('success_event', 'Vielen Dank für die Buchung. Wir werden uns so schnell wie möglich melden.');
    }

    public function searchResponseCity(Request $request)
    {
        // $query = $request->get('term','');
        $cities = City::search($request->get('term'))->get();
        foreach ($cities as $city) {
                $data[]=array('name'=>$city->name,'plz'=>$city->plz,'id'=>$city->id);
        }
        if(count($data))
             return $data;
        else
            return ['name'=>'','plz'=>'','id'=>''];
    }
}
