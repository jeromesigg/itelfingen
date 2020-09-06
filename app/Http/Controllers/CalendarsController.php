<?php

namespace App\Http\Controllers;

use App\Event;
use App\Homepage;
use Redirect,Response;
use Illuminate\Http\Request;

class CalendarsController extends Controller
{
    //
    public function index()
    {
    }
    
   
    public function create(Request $request)
    {  
        
        Event::create($request->all());
        return redirect()->back()->with('success', 'Vielen Dank für die Nachricht. Wir werden uns so schnell wie möglich melden.');
    }
     
 
    public function update(Request $request)
    {   
    } 
}
