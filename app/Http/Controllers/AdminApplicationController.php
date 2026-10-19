<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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

        $data = $applications->map(fn (Application $application) => $this->row($application));

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
     * Bewerbung ablehnen; die Tabelle ruft das per fetch auf und erhält die neue Zeile.
     */
    public function refuse(Request $request, Application $application): JsonResponse|RedirectResponse
    {
        $application->update(['refuse' => true]);

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Bewerbung abgelehnt.', 'row' => $this->row($application->fresh())]);
        }

        return redirect('/admin/applications');
    }

    /**
     * Zeile der Tabelle (DataTable.vue). Aktionen als Daten, die Tabelle führt sie per fetch aus.
     *
     * @return array<string, mixed>
     */
    private function row(Application $application): array
    {
        return [
            'id' => $application->id,
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
            'actions' => $application['invoice_send'] || $application['refuse'] ? [] : [[
                'label' => 'Ablehnen',
                'url' => route('applications.refuse', $application),
                'variant' => 'danger',
                'confirm' => 'Bewerbung von '.trim($application['firstname'].' '.$application['name']).' ablehnen?',
            ]],
        ];
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
