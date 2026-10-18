<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $title = 'Bewerbungen';

        return view('admin.applications.index', compact('title'));
    }

    public function createDataTablesTanStack(Request $request)
    {

        $applications = Application::get();

        // Transformation der Daten (gleich wie Yajra, aber manuel)
        $data = $applications->map(function (Application $application) {
            return [
                'created_at' => [
                    'display' => Carbon::parse($application['created_at'])->format('d.m.Y'),
                    'sort' => Carbon::parse($application['created_at'])->toISOString(),
                ],
                // HTML-Spalten (meta.html): Werte aus dem öffentlichen Formular escapen
                'name' => '<a class="text-orientalpink" href="'.route('applications.show', $application).'">'.e($application['name']).'</a>',
                'firstname' => $application['firstname'],
                'email' => $application['email'],
                'group' => $application['group'],
                'street' => $application['street'].' '.$application['house_number'],
                'city' => $application['plz'].' '.$application['city'],
                'why' => $application['why'],
                'comment' => $application['comment'],
                'refuse' => $application['refuse'] ? 'Ja' : 'Nein',
                'invoice_send' => $application['invoice_send'] ? 'Ja' : 'Nein',
                'members' => $application['members'] ? 'Ja' : 'Nein',
                'actions' => '<form action="'.\URL::route('applications.refuse', $application).'" method="post">'.csrf_field().(! $application['invoice_send'] && ! $application['refuse'] ? '  <button type="submit" class="text-white bg-danger box-border border border-transparent hover:bg-danger-strong focus:ring-4 focus:ring-danger-medium shadow-xs font-medium leading-5 rounded-full text-sm px-4 py-2.5 focus:outline-none">Ablehnen</button>' : '').'</form>',
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
     * @param  int  $id
     * @return Response
     */
    public function show(Application $application)
    {
        //
        $title = 'Bewerbung anzeigen';

        return view('admin.applications.show', compact('application', 'title'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit() {}

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function refuse(Application $application)
    {
        //
        $input['refuse'] = true;

        $application->update($input);

        return redirect('/admin/applications');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
