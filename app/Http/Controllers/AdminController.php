<?php

namespace App\Http\Controllers;

use App\Contact;
use App\Event;
use Illuminate\Http\Request;

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
    
    public function index(){
        $events_all = Event::where('event_status_id','<',config('status.event_eigene'))->count();
        $events_new = Event::where('event_status_id',config('status.event_neu'))->orderBy('start_date')->get();
        $contract_open = Event::where('contract_status_id',config('status.contract_versendet'))->count();
        $contacts_new = Contact::where('done',false)->orderBy('created_at')->get();

        $icon_array = collect([
            (object) [
                'icon' => 'icon-home',
                'name' => 'Total Buchungen',
                'number' => $events_all
            ],
            (object) [
                'icon' => 'icon-padnote',
                'name' => 'Offene Buchungen',
                'number' => count($events_new)
            ],
            (object) [
                'icon' => 'icon-check',
                'name' => 'Verträge unterwegs',
                'number' => $contract_open
            ],   
            (object) [
                'icon' => 'icon-ios-email-outline',
                'name' => 'Offene Anfragen',
                'number' => count($contacts_new)
            ],     
        ]);
        
        return view('admin/index', compact('icon_array', 'contacts_new', 'events_new'));
    }
}
