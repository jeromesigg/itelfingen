<?php

namespace App\Http\Controllers;

use DateTime;
use App\Event;
use App\Http\Requests\EventRequest;
use Illuminate\Support\Facades\Mail;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event as ICal_Event;

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

    public function get_ical_public()
    {         
        $events_ical = [];
        $events = Event::all();
        foreach($events as $event){
            array_push($events_ical, 
                ICal_Event::create($event->groups . ' - ' . $event->firstname . '  ' . $event->name)
                ->period(new DateTime($event->start_date), new DateTime($event->end_date)));
        }

        $calendar = Calendar::create()
            ->name('Ferien- und Lagerhaus Itelfingen')
            ->description('Public Kalender von Itelfingen')
            ->event($events_ical);

        return response($calendar->get(), 200, [
            'Content-Type' => 'text/calendar',
            'Content-Disposition' => 'attachment; filename="calendar.ics"',
            'charset' => 'utf-8',
        ]);
    }


}
