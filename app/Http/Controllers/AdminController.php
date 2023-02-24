<?php

namespace App\Http\Controllers;

use App\Charts\BookingChart;
use App\Helper\Helper;
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
        return view('admin/index', compact('icon_array', 'contacts_new', 'events', 'title'));
    }

    public function changes()
    {
        return view('admin/changes');
    }

    public function bookings()
    {

        $events_nights_year = Event::select(
            DB::raw('sum(total_days) as days'),
            DB::raw('sum(total_people*total_days) as stays'),
            DB::raw("DATE_FORMAT(start_date, '%Y') as timeframe"),
        )
            ->groupBy('timeframe')
            ->orderBy('start_date', 'ASC');

        $bookingChartYear = Helper::getChart($events_nights_year);


        $events_nights_quarter = Event::select(
            DB::raw('sum(total_days) as days'),
            DB::raw('sum(total_people*total_days) as stays'),
            DB::raw("concat(DATE_FORMAT(start_date, '%Y'),'-Q', QUARTER(start_date)) as timeframe"),
        )
            ->groupBy('timeframe')
            ->orderBy('start_date', 'ASC');
        $bookingChartQuarter = Helper::getChart($events_nights_quarter);

        $title='Auslastung';
        return view('admin/bookings', compact( 'bookingChartYear', 'bookingChartQuarter','title'));
    }
}
