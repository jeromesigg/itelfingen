<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('home');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//fullcalender
Route::post('calendar/create','CalendarsController@create');

Route::get('/document/convert-word-to-pdf', 'DocumentController@convertWordToPDF')->name('document.wordtopdf');


Route::group(['middleware' => 'admin'], function(){
    Route::get('/admin','AdminController@index');
    Route::resource('admin/homepages','AdminHomepageController');
    Route::resource('admin/albums', 'AdminAlbumsController');
    Route::resource('admin/pictures', 'AdminPicturesController');
    Route::resource('admin/eventstatuses', 'AdminEventStatusesController');
    Route::resource('admin/pricelists', 'AdminPricelistController');
    Route::resource('admin/testimonials', 'AdminTestimonialController');
    Route::resource('admin/people', 'AdminPersonController');
    Route::resource('admin/histories', 'AdminHistoryController');
    Route::resource('admin/users', 'AdminUserController');


});

Route::get('admin/run-migrations', function () {
    return Artisan::call('migrate', ["--force" => true ]);
});