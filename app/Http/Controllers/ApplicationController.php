<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\Homepage;
use App\Models\Salutation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use jeremykenedy\Slack\Laravel\Facade as Slack;
use Spatie\GoogleCalendar\Event as Event_API;

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
        $salutations = Salutation::pluck('name','id');
        $title = "Bewerbung Genossenschaft";

        return view('contents.applications', compact('homepage', 'title', 'salutations'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
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
        $input = $request->all();

        $input['plz'] = $input['zipcode'];
        $name = $input['firstname'] . ' ' . $input['name'];
        $email = $input['email'];
        $application = Application::create($input);

        if (config('app.env') == 'production') {
            $application = CreateContact($application);
            Slack::to('#5_genossenschaft')->send('Es hat eine neue Bewerbung gegeben. '.$application['firstname'] . ' ' . $application['name'] . ' würde gerne Genossenschafter/in werden. Grund: ' . $application['why']);
        }

        Mail::send('emails.send_application',  $input, function($message) use($email, $name){
            $message
                ->to($email, $name)
                ->replyto(config('mail.from.address_application'), config('mail.from.name'))
                ->subject('Deine Bewerbung für die Genossenschaft Ferienhaus Itelfingen');
        });


        return redirect()->to(url()->previous())->with('success', 'Vielen Dank für deine Bewerbung. Wir werden sie überprüfen und melden uns in zwei Wochen.');
    }

    public function CreateContact(Application $application)
    {
        if(is_null($application['bexio_user_id'])){
            $query = array(
                array(
                    'field' => 'name_1',
                    'value' => $application->name
                ),
                array(
                    'field' => 'name_2',
                    'value' => $application->firstname ? $application->firstname  : ''
                ),
                array(
                    'field' => 'address',
                    'value' => $application->street
                ),
                array(
                    'field' => 'postcode',
                    'value' => $application->plz
                ),);
            $person = Curl::to('https://api.bexio.com/2.0/contact/search')
                ->withHeader('Accept: application/json')
                ->withBearer(config('app.bexio_token'))
                ->withContentType('application/json')
                ->withData($query)
                ->asJson(true)
                ->post();

            if(count($person) === 0){
                $person = Curl::to('https://api.bexio.com/2.0/contact')
                    ->withHeader('Accept: application/json')
                    ->withBearer(config('app.bexio_token'))
                    ->withContentType('application/json')
                    ->withData( array(
                        'contact_type_id' => '2',
                        'name_1' => $application->name,
                        'name_2' => $application->firstname,
                        'address' => $application->street,
                        'postcode' => $application->plz,
                        'city' => $application->city,
                        'country_id' => 1,
                        'mail' => $application->email,
                        'phone_mobile' => $application->telephone,
                        'remarks' => $application->comment,
                        'user_id' => 1,
                        'owner_id' => 1,
                    ) )
                    ->asJson(true)
                    ->post();
            }
            if(!isset($person->error)){
                $application->update(['bexio_user_id' => $person['id']]);
            }
        }
        return $application;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function show(Application $application)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function edit(Application $application)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Application $application)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Application  $application
     * @return \Illuminate\Http\Response
     */
    public function destroy(Application $application)
    {
        //
    }
}
