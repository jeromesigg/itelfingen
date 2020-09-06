<?php

namespace App\Http\Controllers;

use App\Album;
use App\Event;
use App\Picture;
use App\Homepage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

     /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $homepage = Homepage::FindOrFail(1);
        $pictures = Picture::paginate(6);
        $albums = Album::all();
        $events = Event::all();

        
        $events_json = [];
        foreach ($events as $event)
        {
            $events_json[] = [
                'title' => $event['title'] ? $event['title'] : '',
                'start' => $event['start_date'],
                'end' => $event['end_date']
            ];
        }
        return view('home', compact('homepage', 'pictures', 'albums', 'events_json'));
    }
}
