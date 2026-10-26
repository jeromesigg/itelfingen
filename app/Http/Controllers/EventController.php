<?php

namespace App\Http\Controllers;

use App\Enums\BookingState;
use App\Enums\ContractState;
use App\Events\EventCreated;
use App\Models\City;
use App\Models\Event;
use App\Notifications\EventCreatedNotification;
use App\Services\AdminPush;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Notification;
use Validator;

class EventController extends Controller
{
    public function create(Request $request, AdminPush $adminPush)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'zipcode' => 'required|numeric|gte:1000|lte:99999',
            'city' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            // Die Checkbox schickt einen leeren Wert; nicht angehakt wird sie gar nicht gesendet.
            'terms' => 'present',
            // Mindestens eine Übernachtung (Tagesaufenthalte gibt es nicht mehr)
            'total_days' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'total_people' => 'required|gt:0|lte:19',
            'positions' => 'nullable|array',
            'positions.*' => 'nullable|numeric|min:0',
        ], [
            'terms.present' => 'Bitte akzeptiere die Hausordnung.',
            'zipcode.numeric' => 'Die Postleitzahl muss numerisch sein.',
            'zipcode.gte' => 'Die Postleitzahl muss grösser oder gleich 1000 sein.',
            'zipcode.lte' => 'Die Postleitzahl muss kleiner oder gleich 9999 sein.',
            'total_days.min' => 'Es muss mindestens eine Übernachtung gewählt werden.',
            'end_date.after' => 'Es muss mindestens eine Übernachtung gewählt werden.',
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
            'comment', 'marketing_comment', 'terms', 'start_date', 'end_date', 'total_days', 'total_people', 'total_amount',
            'positions',
        ]);
        $input['plz'] = $input['zipcode'];
        $input['group_name'] = $input['group'] ?? null;
        $input['event_status_id'] = BookingState::Neu;
        $input['contract_status_id'] = ContractState::Offen;
        $input['start_date'] = new Carbon($input['start_date']);
        $input['end_date'] = new Carbon($input['end_date']);
        // Rabatt berechnet der Server selbst; der Wert aus dem Formular ist nur Anzeige und manipulierbar
        $input['discount'] = $this->shortNoticeDiscount($input['start_date']);
        $input['uuid'] = Str::uuid();
        $input['phonenumber_query'] = str_replace([' ', '-'], '', $input['telephone'] ?? '');
        $event = Event::create($input);
        EventCreated::dispatch($event, false, $input['positions'] ?? []);
        Notification::send($event, new EventCreatedNotification($event));
        $adminPush->eventCreated($event);

        return redirect()->to(url()->previous().'#booking')->with('success_event', 'Vielen Dank für die Buchung. Wir werden uns so schnell wie möglich melden. Schau auch in deinem Spam-Ordner nach.');
    }

    /**
     * 50 % Rabatt für kurzfristige Buchungen (Anreise in weniger als 8 Tagen), wenn in der Config eingeschaltet.
     * Gleiche Regel wie in der Anzeige des Buchungsformulars (event_js.blade.php, getDays).
     */
    private function shortNoticeDiscount(Carbon $start): int
    {
        $enabled = filter_var(config('app.discount_enabled'), FILTER_VALIDATE_BOOLEAN);

        return $enabled && $start->lt(Carbon::today()->addDays(8)) ? 50 : 0;
    }

    public function searchResponseCity(Request $request)
    {
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
