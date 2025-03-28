<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Faq;
use App\Models\Event;
use App\Models\EventCheckpoint;
use App\Models\Person;
use App\Models\Picture;
use App\Models\Homepage;
use App\Models\FaqChapter;
use Illuminate\Http\Request;
use App\Models\PricelistPosition;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    // public function __construct()
    // {
    //     $this->middleware('auth');
    // }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $homepage = Homepage::FindOrFail(1);
        $pictures = Picture::all();
        $positions = PricelistPosition::where([['show', true], ['archive_status_id', config('status.aktiv')]])->orderby('bexio_code')->get();
        $events = Event::where('event_status_id', '<', config('status.event_storniert'))->where('end_date', '>=', Carbon::now())->get();
        $event_type = 'guest';
        $discount = config('app.discount_enabled');
        $application_enabled = config('app.application_enabled');

        $events_json = [];
        foreach ($events as $event) {
            $start_date = new Carbon($event['start_date']);
            $end_date = new Carbon($event['end_date']);
            $start = [
                'y' => $start_date->year,
                'm' => $start_date->month - 1,
                'd' => $start_date->day,
                'h' => ! ($event['early_checkin'] || ($start_date->dayOfWeek === 0)),
            ];
            $end = [
                'y' => $end_date->year,
                'm' => $end_date->month - 1,
                'd' => $end_date->day,
                'h' => ! ($event['late_checkout'] || ($end_date->dayOfWeek === 0)),
            ];
            $events_json[] = [
                'start' => $start,
                'end' => $end,
                'state' => $event->event_status['color'],
                'id' => $event->id,
            ];
        }
        $contact_attributes = [
            'data-theme' => 'light',
            'data-callback' => 'enable_ContactBtn',
        ];
        $event_attributes = [
            'data-theme' => 'light',
            'data-callback' => 'enable_EventBtn',
        ];

        return view('home', compact('homepage', 'pictures', 'events_json', 'event_type', 'event_attributes', 'contact_attributes', 'positions', 'discount', 'application_enabled'));
    }

    public function impressum()
    {
        $homepage = Homepage::FindOrFail(1);

        return view('contents.impressum', compact('homepage'));
    }

    public function about_us()
    {
        $homepage = Homepage::FindOrFail(1);
        $people = Person::where('archive_status_id', config('status.aktiv'))->orderby('sort-index')->get();

        return view('contents.about_us', compact('homepage', 'people'));
    }

    public function faq()
    {
        $homepage = Homepage::FindOrFail(1);
        $title = 'FAQ';
        $faqs = Faq::where('archive_status_id', config('status.aktiv'))->orderby('sort-index')->get();
        $faq_chapters = FaqChapter::where('archive_status_id', config('status.aktiv'))->orderby('sort-index')->get();

        return view('contents.faq', compact('homepage', 'faq_chapters', 'faqs', 'title'));
    }

    public function bookings_login()
    {
        $homepage = Homepage::FindOrFail(1);
        $title = 'Login Kundenbereich';

        return view('contents.bookings_login', compact('homepage', 'title'));
    }

    public function bookings_uuid(string $uuid)
    {
        $homepage = Homepage::FindOrFail(1);
        $event = Event::where('uuid', $uuid)->firstOrFail();
        if ($event === null) {
            return redirect()->route('bookings.login')->withErrors('message', 'Die eingegebenen Daten sind nicht korrekt.');
        }
        $title = 'Deine Buchung Nr. '. str_pad($event['id'], 5, '0', STR_PAD_LEFT);

        return view('contents.bookings_show', compact('homepage', 'title', 'event'));
    }

    public function bookings_check(Request $request)
    {
        $input = $request->all();    
        $event = Event::where('id', intval($input['id']))->where('plz', $input['plz'])->first();
        if ($event === null) {
            return redirect()->route('bookings.login')->withErrors('message', 'Die eingegebenen Daten sind nicht korrekt.')->withInput();
        }

        return redirect()->route('bookings.uuid', ['uuid' => $event['uuid']]);
    }

    public function bookings_checklist(string $uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        if ($event === null) {
            return redirect()->route('bookings.login')->withErrors('message', 'Die eingegebenen Daten sind nicht korrekt.');
        }
        $homepage = Homepage::FindOrFail(1);
        $title = 'Deine Buchung Nr. '. str_pad($event['id'], 5, '0', STR_PAD_LEFT);

        return view('contents.bookings_checklist', compact('homepage', 'title', 'event'));
    }

    public function DownloadLastInfos(string $uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        if ($event === null) {
            return redirect()->route('bookings.login')->withErrors('message', 'Die eingegebenen Daten sind nicht korrekt.');
        }
        $outputFile = Storage::disk('local')->path('contracts/Infos_vor_Buchung.pdf');

        return response()->download($outputFile);
    }

    public function bookings_checkpointDone(Request $request)
    {
        $checkpoint_id = $request->get('checkpoint_id');
        $checkpoint = EventCheckpoint::findOrFail($checkpoint_id);
        $checkpoint->done = !$checkpoint->done;
        // $checkpoint->update($input);
        return response()->json([
            'data' => [
                'done' =>  $checkpoint->done,
                'success' => $checkpoint->save(),
            ]
          ]);
    }
}
