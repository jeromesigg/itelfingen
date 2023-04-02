<?php

namespace App\Http\Controllers;

use App\Helper\Helper;
use App\Models\Contact;
use App\Models\Event;

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

        $bookingChartYear = Helper::getChart('yearly');
        $bookingChartQuarter = Helper::getChart('quarter');
        $bookingChartMonthly = Helper::getChart('monthly');

        $title='Auslastung';
        return view('admin/bookings', compact( 'bookingChartYear', 'bookingChartQuarter','title', 'bookingChartMonthly'));
    }

    public function exportCSV(Request $request)
    {
        $fileName = 'export.csv';
        $bookingChart = Helper::getChart('daily');

        $headers = array(
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $columns = array('Datum', '# Nächte', '# Übernachtungen');

        $callback = function() use($bookingChart, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($bookingChart as $chart) {
                $row['date']  = $chart->time_frage;
                $row['days']    = $chart->days;
                $row['stays']    = $chart->stays;

                fputcsv($file, array($row['date'], $row['days'], $row['stays']));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
