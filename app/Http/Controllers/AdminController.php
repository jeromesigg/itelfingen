<?php

namespace App\Http\Controllers;

use App\Charts\BookingChart;
use App\Models\Contact;
use App\Models\Event;
use DB;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $events_all = Event::where('event_status_id', '<', config('status.event_eigene'))->count();
        $events_new = Event::where('event_status_id', config('status.event_neu'))->count();
        $events = Event::where('start_date', '>=', today())->where('event_status_id', '<>', config('status.event_storniert'))->orderBy('id', 'DESC')->paginate(5);

        $contacts_new = Contact::where('done', false)->orderBy('created_at', 'DESC')->get();

        $events_nights = Event::select(
            DB::raw('sum(total_days) as days'),
            DB::raw('sum(total_people*total_days) as stays'),
            DB::raw("DATE_FORMAT(start_date, '%Y') as years"),
        )
            ->groupBy('years')
            ->orderBy('start_date', 'ASC');

        $years = $events_nights->pluck('years');
        $days = $events_nights->pluck('days');
        $stays = $events_nights->pluck('stays');

        $bookingChart = new BookingChart;
        $bookingChart->minimalist(true);
        $bookingChart->labels($years);
        $bookingChart->dataset('Anzahl Tage', 'line', $days)
            ->color( '#92D1C3')
            ->backgroundColor( '#92D1C3');
        $bookingChart->dataset('Anzahl Übernachtungen', 'line', $stays)
            ->color('#B47EB3')
            ->backgroundColor('#B47EB3');


        $icon_array = collect([
            (object) [
                'icon' => 'icon-home',
                'name' => 'Total Buchungen',
                'number' => $events_all,
            ],
            (object) [
                'icon' => 'icon-padnote',
                'name' => 'Offene Buchungen',
                'number' => $events_new,
            ],
            (object) [
                'icon' => 'icon-ios-email-outline',
                'name' => 'Offene Anfragen',
                'number' => count($contacts_new),
            ],
        ]);

        $title='Dashboard';
        return view('admin/index', compact('icon_array', 'contacts_new', 'events', 'title', 'bookingChart'));
    }

    public function changes()
    {
        return view('admin/changes');
    }
}
