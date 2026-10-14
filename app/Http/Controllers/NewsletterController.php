<?php

namespace App\Http\Controllers;

use App\Enums\BookingState;
use App\Enums\ContractState;
use App\Exports\ExportNewsletterBookings;
use App\Exports\ExportNewsletterMembers;
use App\Models\Event;
use App\Models\Newsletter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Maatwebsite\Excel\Facades\Excel;

class NewsletterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $title = 'Newsletter';

        return view('admin.newsletter.index', compact('title'));
    }

    public function createDataTablesTanStack(Request $request)
    {

        $newsletter = Newsletter::get();

        // Transformation der Daten (gleich wie Yajra, aber manuel)
        $data = $newsletter->map(function (Newsletter $newsletter) {
            return [
                // HTML-Spalte (meta.html): Namen stammen aus Buchungen und Bewerbungen, also escapen
                'name' => '<a class="text-orientalpink" href="'.route('newsletter.edit', $newsletter).'">'.e($newsletter['name']).'</a>',
                'firstname' => $newsletter['firstname'],
                'email' => $newsletter['email'],
                'bookings' => $newsletter['bookings'] ? 'Ja' : 'Nein',
                'members' => $newsletter['members'] ? 'Ja' : 'Nein',
            ];
        });

        // TanStack-freundliches Response-Format
        return response()->json([
            'data' => $data,
            'rowCount' => count($data),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(Newsletter $newsletter)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
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
     * @return Response
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
     * @return Response
     */
    public function destroy(Newsletter $newsletter)
    {
        //
        $newsletter->delete();

        return redirect('/admin/newsletter');
    }

    public function import()
    {
        $events = Event::where('event_status_id', BookingState::Bestaetigt)->where('contract_status_id', '>=', ContractState::RechnungErstellt->value)->get();
        foreach ($events as $event) {
            Newsletter::updateOrCreate(
                ['email' => $event['email']],
                [
                    'firstname' => $event['firstname'],
                    'name' => $event['name'],
                    'bookings' => true,
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
