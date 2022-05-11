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

Route::get('impressum', 'HomeController@impressum')->name('impressum');
Route::get('faq', 'HomeController@faq')->name('faq');
Route::get('applications', 'ApplicationController@index')->name('applications');
Route::post('applications/store', 'ApplicationController@store');
Route::get('/', 'HomeController@index')->name('home');

Auth::routes();


// Route::get('/home', 'HomeController@index')->name('home');

//fullcalender
Route::post('event/create','EventController@create');
Route::get('event/searchajaxcity', ['as'=>'searchajaxcity','uses'=>'EventController@searchResponseCity']);

Route::post('contacts', 'ContactController@store');

Route::group(['middleware' => 'admin'], function(){

    Route::get('/admin','AdminController@index');
    Route::get('/admin/changes','AdminController@changes');
    Route::resource('admin/homepages','AdminHomepageController');
    Route::resource('admin/pictures', 'AdminPicturesController');
    Route::resource('admin/eventstatuses', 'AdminEventStatusesController');
    Route::resource('admin/testimonials', 'AdminTestimonialController');
    Route::resource('admin/people', 'AdminPersonController');
    Route::resource('admin/histories', 'AdminHistoryController');
    Route::resource('admin/users', 'AdminUserController');
    Route::get('admin/users/download/{user}', ['as'=>'download_signature','uses'=>'AdminUserController@get_signature']);
    Route::resource('admin/events', 'AdminEventController');
    Route::get('events/createDataTables', ['as'=>'events.CreateDataTables','uses'=>'AdminEventController@createDataTables']);
    Route::get('admin/events/{event}/createoffer', 'AdminEventController@CreateOffer')->name('events.createoffer');
    Route::get('admin/events/{event}/sendoffer', 'AdminEventController@SendOffer')->name('events.sendoffer');
    Route::get('admin/events/{event}/createinvoice', 'AdminEventController@CreateInvoice')->name('events.createinvoice');
    Route::get('admin/events/{event}/sendinvoice', 'AdminEventController@SendInvoice')->name('events.sendinvoice');
    Route::post('admin/events/{event}/SendCleaningMail', 'AdminEventController@SendCleaningMail')->name('events.sendcleaningmail');
    Route::resource('admin/contacts', 'AdminContactController');
    Route::resource('admin/faqs', 'AdminFaqController');
    Route::resource('admin/faq_chapters', 'AdminFaqChapterController');
    Route::resource('admin/positions', 'AdminPricelistPositionController');
    Route::resource('admin/applications', 'AdminApplicationController');
    Route::get('applications/createDataTables', ['as'=>'applications.CreateDataTables','uses'=>'AdminApplicationController@createDataTables']);
});

Route::get('admin/run-migrations', function () {
    return Artisan::call('migrate', ["--force" => true ]);
});

Route::get('admin/run-migrations-seed', function () {
    return Artisan::call('migrate --seed', ["--force" => true ]);
});

Route::get('admin/run-deployment', function () {
    echo 'config:cache <br>';
    Artisan::call('config:cache');
    echo 'view:cache <br>';
    Artisan::call('view:cache');
    return true;
});
