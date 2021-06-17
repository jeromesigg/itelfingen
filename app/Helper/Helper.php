<?php

namespace App\Helper;

use App\Event;
use Illuminate\Http\Response;
use ReCaptcha\RequestMethod\Post;
use Illuminate\Support\Facades\Storage;

class Helper
{

    static function getPDF($filename){
    	$file=Storage::disk('public')->get($filename);
 
		return (new Response($file, 200))
              ->header('Content-Type', 'application/pdf');
    }

    static function getWord($filename){
    	$file=Storage::disk('public')->get($filename);
 
		return (new Response($file, 200))
              ->header('Content-Type','application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    }

    static function customRequestCaptcha(){
        return new \ReCaptcha\RequestMethod\Post();
    }

    static function getEventsICalObject(){
        $events = Event::all();
        define('ICAL_FORMAT', 'Ymd\THis\Z');

        $icalObject = "BEGIN:VCALENDAR
        VERSION:2.0
        METHOD:PUBLISH
        PRODID:-//Charles Oduk//Tech Events//EN\n";
        
        // loop over events
        foreach ($events as $event) {
            $icalObject .=
            "BEGIN:VEVENT
            DTSTART:" . date(ICAL_FORMAT, strtotime($event->start_date)) . "
            DTEND:" . date(ICAL_FORMAT, strtotime($event->end_date)) . "
            DTSTAMP:" . date(ICAL_FORMAT, strtotime($event->created_at)) . "
            SUMMARY:$event->group_name
            UID:$event->id
            STATUS:" . strtoupper($event->event_status['name']) . "
            LAST-MODIFIED:" . date(ICAL_FORMAT, strtotime($event->updated_at)) . "
            LOCATION:$event->name
            END:VEVENT\n";
        }

        // close calendar
        $icalObject .= "END:VCALENDAR";

        // Set the headers
        header('Content-type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="cal.ics"');
        
        $icalObject = str_replace(' ', '', $icalObject);
    
        return $icalObject;    
    }
}
