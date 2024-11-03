<?php

namespace App\Http\Controllers;

use App\Models\Newsletter;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\ExportNewsletterBookings;
use App\Exports\ExportNewsletterMembers;
use App\Models\Event;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $title = 'Newsletter';

        return view('admin.newsletter.index', compact('title'));
    }

    public function createDataTables()
    {
        $applications = Newsletter::get();

        return DataTables::of($applications)
            ->addColumn('name', function (Newsletter $newsletter) {
                return '<a href='.\URL::route('newsletter.edit', $newsletter).'>'.$newsletter['name'].'</a>';
            })
            ->addColumn('bookings', function (Newsletter $newsletter) {
                return $newsletter['bookings'] ? 'Ja' : 'Nein';
            })
            ->addColumn('members', function (Newsletter $newsletter) {
                return $newsletter['members'] ? 'Ja' : 'Nein';
            })
            ->rawColumns(['name'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Newsletter  $newsletter
     * @return \Illuminate\Http\Response
     */
    public function show(Newsletter $newsletter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Newsletter  $newsletter
     * @return \Illuminate\Http\Response
     */
    public function edit(Newsletter $newsletter)
    {
        //
        $title = 'Newsletter Empfänger anzeigen';

        return view('admin.newsletter.edit', compact('newsletter', 'title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Newsletter  $newsletter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Newsletter $newsletter)
    {
        //
        $input = $request->all();
        $input['bookings'] = $request->has('bookings');
        $input['members'] = $request->has('members');

        $newsletter->update($input);

        return redirect('/admin/newsletter');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Newsletter  $newsletter
     * @return \Illuminate\Http\Response
     */
    public function destroy(Newsletter $newsletter)
    {
        //
        $newsletter->delete();

        return redirect('/admin/newsletter');
    }

    public function import()
    {
        $events = Event::where('event_status_id', config('status.event_bestaetigt'))->where('contract_status_id', config('status.contract_rechnung_erstellt'))->get();
        foreach($events as $event){
            Newsletter::updateOrCreate(
                ['email' => $event['email']],
                [
                'firstname' => $event['firstname'],
                'name' => $event['name'],
                'bookings' => true
            ]);
        }
        return redirect('/admin/newsletter');
    }
    
    public function exportBookings()
    {
        return Excel::download(new ExportNewsletterBookings, 'newsletter_bookings.csv', \Maatwebsite\Excel\Excel::CSV);
    }
    
    public function exportMembers()
    {
        return Excel::download(new ExportNewsletterMembers, 'newsletter_members.csv', \Maatwebsite\Excel\Excel::CSV);
    }
}
