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
                'icon' => '        <svg class="h-6 w-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
          <path d="M9 7V2.2a2 2 0 0 0-.5.4l-4 3.9a2 2 0 0 0-.3.5H9Z" />
          <path fill-rule="evenodd" d="M11 7V2h7a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V9h5a2 2 0 0 0 2-2Zm4.7 5.7a1 1 0 0 0-1.4-1.4L11 14.6l-1.3-1.3a1 1 0 0 0-1.4 1.4l2 2c.4.4 1 .4 1.4 0l4-4Z" clip-rule="evenodd" />
        </svg>',
                'name' => 'Total Buchungen',
                'number' => $events_all,
                'color' => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-400',
            ],
            (object) [
                'icon' => '<svg class="h-6 w-6" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
          <path fill-rule="evenodd" d="M8 7V2.2a2 2 0 0 0-.5.4l-4 3.9a2 2 0 0 0-.3.5H8Zm2 0V2h7a2 2 0 0 1 2 2v.1a5 5 0 0 0-4.7 1.4l-6.7 6.6a3 3 0 0 0-.8 1.6l-.7 3.7a3 3 0 0 0 3.5 3.5l3.7-.7a3 3 0 0 0 1.5-.9l4.2-4.2V20a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V9h5a2 2 0 0 0 2-2Z" clip-rule="evenodd"/>
          <path fill-rule="evenodd" d="M17.4 8a1 1 0 0 1 1.2.3 1 1 0 0 1 0 1.6l-.3.3-1.6-1.5.4-.4.3-.2Zm-2.1 2.1-4.6 4.7-.4 1.9 1.9-.4 4.6-4.7-1.5-1.5ZM17.9 6a3 3 0 0 0-2.2 1L9 13.5a1 1 0 0 0-.2.5L8 17.8a1 1 0 0 0 1.2 1.1l3.7-.7c.2 0 .4-.1.5-.3l6.6-6.6A3 3 0 0 0 18 6Z" clip-rule="evenodd"/>
        </svg>',
                'name' => 'Offene Buchungen',
                'number' => $events_new,
                'color' => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-400',
            ],
            (object) [
                'icon' => '<svg class="h-6 w-6 " aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
          <path fill-rule="evenodd" d="M5 5c.6 0 1-.4 1-1a1 1 0 1 1 2 0c0 .6.4 1 1 1h1c.6 0 1-.4 1-1a1 1 0 1 1 2 0c0 .6.4 1 1 1h1c.6 0 1-.4 1-1a1 1 0 1 1 2 0c0 .6.4 1 1 1a2 2 0 0 1 2 2v1c0 .6-.4 1-1 1H4a1 1 0 0 1-1-1V7c0-1.1.9-2 2-2ZM3 19v-7c0-.6.4-1 1-1h16c.6 0 1 .4 1 1v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2Zm6-6c0-.6-.4-1-1-1a1 1 0 1 0 0 2c.6 0 1-.4 1-1Zm2 0a1 1 0 1 1 2 0c0 .6-.4 1-1 1a1 1 0 0 1-1-1Zm6 0c0-.6-.4-1-1-1a1 1 0 1 0 0 2c.6 0 1-.4 1-1ZM7 17a1 1 0 1 1 2 0c0 .6-.4 1-1 1a1 1 0 0 1-1-1Zm6 0c0-.6-.4-1-1-1a1 1 0 1 0 0 2c.6 0 1-.4 1-1Zm2 0a1 1 0 1 1 2 0c0 .6-.4 1-1 1a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
        </svg>',
                'name' => 'Offene Anfragen',
                'number' => count($contacts_new),
                'color' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900 dark:text-yellow-400',
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
