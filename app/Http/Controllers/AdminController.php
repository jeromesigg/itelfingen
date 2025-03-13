<?php

namespace App\Http\Controllers;

use App\Exports\ExportBookings;
use App\Helper\Helper;
use App\Models\Contact;
use App\Models\Event;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

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
        $events_current = Event::where('start_date', '>=', Carbon::today()->addWeeks(-2))->where('event_status_id', '<>', config('status.event_storniert'))->orderBy('start_date', 'ASC')->paginate(5);

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

        $title = 'Dashboard';

        return view('admin/index', compact('icon_array', 'contacts_new', 'events', 'title', 'events_current'));
    }

    public function changes()
    {
        return view('admin/changes');
    }

    public function bookings()
    {

        $bookingChartYear = Helper::getChart('yearly');
        $bookingChartQuarter = Helper::getChart('quarter');
        $bookingChartMonthly = Helper::getChart('monthly');

        $title = 'Auslastung';

        return view('admin/bookings', compact('bookingChartYear', 'bookingChartQuarter', 'title', 'bookingChartMonthly'));
    }

    public function exportCSV()
    {
        return Excel::download(new ExportBookings, 'bookings.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
