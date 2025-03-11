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
