<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Homepage;
use App\Models\Salutation;
use App\Notifications\ApplicationCreatedNotification;
use App\Services\AdminPush;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Notification;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
        $homepage = Homepage::FindOrFail(1);
        $salutations = Salutation::pluck('name', 'id');
        $title = 'Bewerbung Genossenschaft';

        return view('contents.applications', compact('homepage', 'title', 'salutations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request, AdminPush $adminPush)
    {
        //
        $request->validate([
            'name' => 'required|string|max:255',
            'street' => 'required|string|max:255',
            'zipcode' => 'required|numeric|gte:1000|lte:9999',
            'city' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'why' => 'required|string',
            'statutes' => 'accepted',
        ], [
            'statutes.accepted' => 'Bitte akzeptiere die Statuten.',
        ]);

        // Nur Felder des Formulars, keine internen Felder (Bexio, Rechnung, Ablehnung).
        $input = $request->only([
            'name', 'firstname', 'organisation', 'street', 'house_number', 'zipcode', 'city', 'email', 'telephone',
            'why', 'comment',
        ]);
        if (! preg_match('/\s/', $input['why'])) {
            abort(response()->view('errors.422', [], 422));
        }

        $input['plz'] = $input['zipcode'];
        $application = Application::create($input);

        Notification::send($application, new ApplicationCreatedNotification($application));
        $adminPush->applicationCreated($application);

        return redirect()->to(url()->previous())->with('success', 'Vielen Dank für deine Bewerbung. Wir werden sie überprüfen und melden uns in zwei Wochen.');
    }

    /**
     * Display the specified resource.
     *
     * @return Response
     */
    public function show(Application $application)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit(Application $application)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return Response
     */
    public function update(Request $request, Application $application)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(Application $application)
    {
        //
    }
}
