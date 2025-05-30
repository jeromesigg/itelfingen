<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('api_token')->get('events/{api_token}', 'AdminEventController@api');
Route::middleware('api_token')->get('events_ical/{api_token}', 'AdminEventController@api_ical');
Route::middleware('api_token')->get('phone_number/{api_token}/number/{number}', 'AdminEventController@getPhoneNumber');
