<?php

namespace App\Http\Controllers;

use App\Events\ApplicationCreatedEvent;
use App\Models\Application;
use App\Models\Homepage;
use App\Models\Salutation;
use App\Notifications\ApplicationCreatedNotification;
use Illuminate\Http\Request;
use Notification;

class ApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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
     * @return \Illuminate\Http\Response
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $input = $request->all();

        $input['plz'] = $input['zipcode'];
        $application = Application::create($input);

        ApplicationCreatedEvent::dispatch($application);
        Notification::send($application, new ApplicationCreatedNotification($application));

        return redirect()->to(url()->previous())->with('success', 'Vielen Dank für deine Bewerbung. Wir werden sie überprüfen und melden uns in zwei Wochen.');
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Application $application)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Application $application)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Application $application)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Application $application)
    {
        //
    }
}
