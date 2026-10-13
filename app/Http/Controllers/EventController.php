<?php

namespace App\Http\Controllers;

use App\Enums\BookingState;
use App\Enums\ContractState;
use App\Events\EventCreated;
use App\Models\City;
use App\Models\Event;
use App\Notifications\EventCreatedNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Notification;
use Validator;

class EventController extends Controller
{
    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'zipcode' => 'required|numeric|gte:1000|lte:9999',
            'city' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'terms' => 'accepted',
            // Tagesbuchung (0 Tage) mit "date", sonst Anreise und Abreise
            'total_days' => 'required|integer|min:0',
            'date' => 'nullable|required_if:total_days,0|date',
            'start_date' => 'nullable|required_unless:total_days,0|date',
            'end_date' => 'nullable|required_unless:total_days,0|date|after_or_equal:start_date',
            'total_people' => 'required|gt:0|lte:19',
            'positions' => 'nullable|array',
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

        // Nur Felder des Buchungsformulars, keine internen Felder (Türcode, Bexio, Notizen, Status).
        $input = $request->only([
            'name', 'firstname', 'group', 'street', 'house_number', 'zipcode', 'city', 'email', 'telephone',
            'comment', 'marketing_comment', 'terms', 'date', 'start_date', 'end_date', 'total_days', 'total_people', 'total_amount',
            'discount', 'positions',
        ]);
        $input['plz'] = $input['zipcode'];
        $input['group_name'] = $input['group'] ?? null;
        $input['event_status_id'] = BookingState::Neu;
        $input['contract_status_id'] = ContractState::Offen;
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
        $input['uuid'] = Str::uuid();
        $input['phonenumber_query'] = str_replace([' ', '-'], '', $input['telephone'] ?? '');
        $event = Event::create($input);
        EventCreated::dispatch($event, $one_day, $input['positions'] ?? []);
        Notification::send($event, new EventCreatedNotification($event));

        return redirect()->to(url()->previous().'#booking')->with('success_event', 'Vielen Dank für die Buchung. Wir werden uns so schnell wie möglich melden. Schau auch in deinem Spam-Ordner nach.');
    }

    public function searchResponseCity(Request $request)
    {
        // $query = $request->get('term','');
        $cities = City::search($request->get('term'))->get();
        $data = [];
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
