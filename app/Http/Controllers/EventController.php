<?php

namespace App\Http\Controllers;

use App\Events\EventCreated;
use App\Models\City;
use App\Models\Event;
use App\Notifications\EventCreatedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Notification;
use Validator;

class EventController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'zipcode' => 'numeric|gte:1000|lte:9999',
            'total_people' => 'gt:0|lte:19',
        ], [
            'zipcode.numeric' => 'Die Postleitzahl muss numerisch sein.',
            'zipcode.gte' => 'Die Postleitzahl muss grösser oder gleich 1000 sein.',
            'zipcode.lte' => 'Die Postleitzahl muss kleiner oder gleich 9999 sein.',
            'total_people.gt' => 'Für die Buchung braucht es mindestens 1 Person.',
            'total_people.lte' => 'Für die Buchung sind maximal 19 Personen erlaubt.',  ]);

        if ($validator->fails()) {
            return redirect()->to(url()->previous().'#booking')
                ->withErrors($validator, 'event')
                ->withInput();
        }

        $input = $request->all();
        $input['plz'] = $input['zipcode'];
        $input['group_name'] = $input['group'];
        $input['event_status_id'] = config('status.event_neu');
        $input['contract_status_id'] = config('status.contract_offen');
        $one_day = false;
        if ($input['total_days'] < 1) {
            $input['start_date'] = new Carbon($input['date']);
            $input['end_date'] = new Carbon($input['date']);
            $input['total_days'] = 1;
            $one_day = true;
        } else {
            $input['start_date'] = new Carbon($input['start_date']);
            $input['end_date'] = new Carbon($input['end_date']);
        }
        $input['uuid'] = \Illuminate\Support\Str::uuid();
        $input['phonenumber_query'] = str_replace(' ', '',$input['telephone']);
        $input['phonenumber_query'] = str_replace('-', '',$input['phonenumber_query']);
        $event = Event::create($input);
        EventCreated::dispatch($event, $one_day, $input['positions']);
        Notification::send($event, new EventCreatedNotification($event));

        return redirect()->to(url()->previous().'#booking')->with('success_event', 'Vielen Dank für die Buchung. Wir werden uns so schnell wie möglich melden. Schau auch in deinem Spam-Ordner nach.');
    }

    public function searchResponseCity(Request $request)
    {
        // $query = $request->get('term','');
        $cities = City::search($request->get('term'))->get();
        foreach ($cities as $city) {
            $data[] = ['name' => $city->name, 'plz' => $city->plz, 'id' => $city->id];
        }
        if (count($data)) {
            return $data;
        } else {
            return ['name' => '', 'plz' => '', 'id' => ''];
        }
    }
}
