<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Spatie\Honeypot\ProtectAgainstSpam;

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
Route::post('applications/store', ['as' => 'application.store', 'uses' => 'ApplicationController@store'])->middleware(ProtectAgainstSpam::class);
Route::get('/', 'HomeController@index')->name('home');
Route::get('about_us', 'HomeController@about_us')->name('about_us');
Route::get('bookings/login', 'HomeController@bookings_login')->name('bookings.login');
Route::post('bookings/login', 'HomeController@bookings_check')->name('bookings.check');
Route::get('bookings/{uuid}', 'HomeController@bookings_uuid')->name('bookings.uuid');
Route::get('bookings/{uuid}/checklist', 'HomeController@bookings_checklist')->name('bookings.checklist');
Route::get('admin/bookings/{uuid}/DownloadLastInfos', 'HomeController@DownloadLastInfos')->name('bookings.downloadLastInfos');
Route::post('bookings/checkpoint/done', 'HomeController@bookings_checkpointDone')->name('bookings.checkpointDone');

Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');

//fullcalender
Route::post('event/create', ['as' => 'event.create', 'uses' => 'EventController@create'])->middleware(ProtectAgainstSpam::class);
Route::get('event/searchajaxcity', ['as' => 'searchajaxcity', 'uses' => 'EventController@searchResponseCity']);

Route::post('contacts', ['as' => 'contacts.store', 'uses' => 'ContactController@store'] )->middleware(ProtectAgainstSpam::class);

Route::group(['middleware' => 'admin'], function () {
    Route::get('/admin', 'AdminController@index');
    Route::get('/admin/changes', ['as' => 'admin.changes', 'uses' => 'AdminController@changes']);
    Route::get('/admin/bookings', ['as' => 'admin.bookings', 'uses' => 'AdminController@bookings']);
    Route::get('/admin/bookings/export-csv', ['as' => 'admin.exportcsv', 'uses' => 'AdminController@exportCSV']);
    Route::patch('admin/homepages/{homepage}/comment_update', 'AdminHomepageController@CommentUpdate')->name('admin.homepages.comment_update');
    Route::resource('admin/homepages', 'AdminHomepageController');
    Route::resource('admin/pictures', 'AdminPicturesController');
    Route::resource('admin/eventstatuses', 'AdminEventStatusesController');
    Route::resource('admin/testimonials', 'AdminTestimonialController');
    Route::resource('admin/people', 'AdminPersonController');
    Route::resource('admin/histories', 'AdminHistoryController');
    Route::resource('admin/users', 'AdminUserController');
    Route::get('admin/users/download/{user}', ['as' => 'download_signature', 'uses' => 'AdminUserController@get_signature']);
    Route::resource('admin/events', 'AdminEventController')->names('admin.events');
    Route::get('events/createDataTables', ['as' => 'events.CreateDataTables', 'uses' => 'AdminEventController@createDataTables']);
    Route::post('admin/events/{event}/SendCleaningMail', 'AdminEventController@SendCleaningMail')->name('events.sendCleaningMail');
    Route::get('admin/events/{event}/DownloadParking', 'AdminEventController@DownloadParking')->name('events.downloadParking');

    Route::resource('admin/contacts', 'AdminContactController')->names('admin.contacts');
    Route::post('contacts/{contact}/done', ['as' => 'contacts.done', 'uses' => 'AdminContactController@done']);
    Route::get('contacts/createDataTables', ['as' => 'contacts.CreateDataTables', 'uses' => 'AdminContactController@createDataTables']);
    Route::resource('admin/faqs', 'AdminFaqController');
    Route::get('faqs/createDataTables', ['as' => 'faqs.CreateDataTables', 'uses' => 'AdminFaqController@createDataTables']);
    Route::resource('admin/faq_chapters', 'AdminFaqChapterController');
    Route::resource('admin/positions', 'AdminPricelistPositionController');
    Route::resource('admin/applications', 'AdminApplicationController');
    Route::get('applications/createDataTables', ['as' => 'applications.CreateDataTables', 'uses' => 'AdminApplicationController@createDataTables']);
    Route::post('applications/{application}/refuse', ['as' => 'applications.refuse', 'uses' => 'AdminApplicationController@refuse']);
    Route::get('newsletter/createDataTables', ['as' => 'newsletter.CreateDataTables', 'uses' => 'NewsletterController@createDataTables']);
    Route::get('/admin/newsletter/export-bookings', ['as' => 'newsletter.exportBookings', 'uses' => 'NewsletterController@exportBookings']);
    Route::get('/admin/newsletter/export-members', ['as' => 'newsletter.exportMembers', 'uses' => 'NewsletterController@exportMembers']);
    Route::get('/admin/newsletter/import', ['as' => 'newsletter.import', 'uses' => 'NewsletterController@import']);
    Route::resource('admin/newsletter', 'NewsletterController');
    Route::resource('admin/checkpoints', 'AdminCheckpointController');
    Route::resource('admin/rooms', 'AdminRoomsController');
});

// Route::get('admin/run-migrations', function () {
//     return Artisan::call('migrate', ['--force' => true]);
// });

// Route::get('admin/run-migrations-seed', function () {
//     return Artisan::call('migrate --seed', ['--force' => true]);
// });

// Route::get('admin/run-deployment', function () {
//     Artisan::call('optimize:clear');

//     return true;
// });
