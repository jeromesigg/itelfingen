<?php

namespace App\Http\Controllers;

use App\Enums\BookingState;
use App\Exports\ExportBookings;
use App\Helper\Helper;
use App\Models\Contact;
use App\Models\Event;
use App\Services\BookingChecks;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(BookingChecks $checks)
    {
        $canManageBookings = Auth::user()->canManageBookings();
        $events = Event::where('start_date', '>=', today())->where('event_status_id', '<>', BookingState::Storniert)->orderBy('id', 'DESC')->limit(5)->get();
        $events_upcoming = Event::where('start_date', '>=', Carbon::today())
            ->where('event_status_id', '<>', BookingState::Storniert)
            ->orderBy('start_date', 'ASC')
            ->limit(2)
            ->get();

        $events_past = Event::where('start_date', '<', Carbon::today())
            ->where('event_status_id', '<>', BookingState::Storniert)
            ->orderBy('start_date', 'DESC')
            ->limit(3)
            ->get();

        // chronologisch zusammenführen: älteste Vergangenheit zuerst, dann Zukunft
        $events_current = $events_past->reverse()->values()->concat($events_upcoming);

        $counters = collect([
            (object) [
                'name' => 'Neu',
                'number' => Event::where('event_status_id', BookingState::Neu)->count(),
                'link' => route('admin.events.index'),
                'color' => 'text-orange-700 dark:text-orange-400',
            ],
            (object) [
                'name' => 'Anfragen',
                'number' => Contact::where('done', false)->count(),
                'link' => $canManageBookings ? route('admin.contacts.index') : null,
                'color' => 'text-yellow-700 dark:text-yellow-400',
            ],
            (object) [
                'name' => 'Total',
                'number' => Event::where('event_status_id', '<', BookingState::Eigene->value)->count(),
                'link' => route('admin.events.index'),
                'color' => 'text-green-700 dark:text-green-400',
            ],
        ]);

        $tasks = $canManageBookings ? $checks->dashboardTasks() : collect();

        $title = 'Dashboard';

        return view('admin/index', compact('counters', 'tasks', 'events', 'title', 'events_current'));
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
        $bookingHeatMap = Helper::getChart('heatmap');

        $title = 'Auslastung';

        return view('admin/bookings', compact('bookingChartYear', 'bookingChartQuarter', 'title', 'bookingChartMonthly', 'bookingHeatMap'));
    }

    public function exportCSV()
    {
        return Excel::download(new ExportBookings, 'bookings.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
