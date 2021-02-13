<?php

namespace App\Http\Controllers;

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
        $events_all = Event::orderby('start_date')->get();
        $events_new = Event::where('event_status_id',config('status.event_neu'))->get();
        
        return view('admin/index', compact('events_all', 'events_new'));
    }
}
