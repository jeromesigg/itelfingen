<?php

namespace App\Helper;

use Illuminate\Http\Response;
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
}
