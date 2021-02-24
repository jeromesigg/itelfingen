<?php

namespace App\Http\Controllers;

use App\Album;
use App\Event;
use App\History;
use App\Picture;
use App\Homepage;
use App\Person;
use App\Pricelist;
use App\Testimonial;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        $pictures = Picture::paginate(6);
        $pricelists = Pricelist::where('archive_status_id', config('status.aktiv'))->orderby('sort-index')->get();
        $testimonials = Testimonial::where('archive_status_id', config('status.aktiv'))->orderby('sort-index')->get();
        $people = Person::where('archive_status_id', config('status.aktiv'))->orderby('sort-index')->get();
        $histories = History::where('archive_status_id', config('status.aktiv'))->orderby('sort-index')->get();
        $albums = Album::all();
        $events = Event::where('event_status_id','<',config('status.event_storniert'))->get();
        $event_type = 'guest';

        
        $events_json = [];
        foreach ($events as $event)
        {
            $start_date = new Carbon($event['start_date']);
            $end_date = new Carbon($event['end_date']);
            $start = [
                'y' => $start_date->year,
                'm' => $start_date->month-1,
                'd' => $start_date->day,
                'h' => true,
            ];
            $end = [
                'y' => $end_date->year,
                'm' => $end_date->month-1,
                'd' => $end_date->day,
                'h' => true,
            ];
            $events_json[] = [
                'start' => $start,
                'end' => $end,
                'state' => $event->event_status['color'],
                'id' => $event->id
            ];
        }
        $contact_attributes = [
            'data-theme' => 'light',
            'data-callback' => 'enable_ContactBtn'
        ];
        $event_attributes = [
            'data-theme' => 'light',
            'data-callback' => 'enable_EventBtn'
        ];
        return view('home', compact('homepage', 'pictures', 'albums', 'events_json', 'pricelists', 'testimonials', 'people', 
            'histories', 'event_type', 'event_attributes', 'contact_attributes'));
    }

    public function impressum()
    {
        $homepage = Homepage::FindOrFail(1);
        
        return view('contents.impressum', compact('homepage'));
    }
}
