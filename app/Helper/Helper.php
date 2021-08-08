<?php

namespace App\Helper;

use App\Event;
use Illuminate\Http\Response;
use ReCaptcha\RequestMethod\Post;
use Illuminate\Support\Facades\Storage;

class Helper
{
    static function customRequestCaptcha(){
        return new \ReCaptcha\RequestMethod\Post();
    }
}
