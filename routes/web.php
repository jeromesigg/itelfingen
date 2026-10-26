<?php

use App\Http\Controllers\AdminEventController;
use App\Http\Controllers\WebManifestController;
use App\Services\BexioAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
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
Route::post('applications/store', ['as' => 'application.store', 'uses' => 'ApplicationController@store'])->middleware(['throttle:public-forms', ProtectAgainstSpam::class]);
Route::get('/', 'HomeController@index')->name('home');
Route::get('about_us', 'HomeController@about_us')->name('about_us');
// Admin-Bereich als installierbare App (Service Worker liegt statisch unter public/admin-sw.js)
Route::get('app.webmanifest', WebManifestController::class)->name('webmanifest');
Route::middleware('throttle:booking-login')->group(function () {
    Route::get('/bookings/login', 'HomeController@bookings_login')->name('bookings.login');
    Route::post('/bookings/login', 'HomeController@bookings_check')->name('bookings.check');
});
Route::get('bookings/{uuid}', 'HomeController@bookings_uuid')->name('bookings.uuid');
Route::get('bookings/{uuid}/checklist', 'HomeController@bookings_checklist')->name('bookings.checklist');
Route::get('admin/bookings/{uuid}/DownloadLastInfos', 'HomeController@DownloadLastInfos')->name('bookings.downloadLastInfos');
Route::get('admin/events/{uuid}/DownloadParking', 'AdminEventController@DownloadParking')->name('events.downloadParking');
Route::post('bookings/{uuid}/checkpoint/done', 'HomeController@bookings_checkpointDone')->name('bookings.checkpointDone');

// Nur Login/Logout: Konten legt ein Admin an, Benutzer haben keine E-Mail-Adresse für einen Reset.
Auth::routes(['register' => false, 'reset' => false, 'confirm' => false]);

// fullcalender
Route::post('event/create', ['as' => 'event.create', 'uses' => 'EventController@create'])->middleware(['throttle:public-forms', ProtectAgainstSpam::class]);
Route::get('event/searchajaxcity', ['as' => 'searchajaxcity', 'uses' => 'EventController@searchResponseCity']);

Route::post('contacts', ['as' => 'contacts.store', 'uses' => 'ContactController@store'])->middleware(['throttle:public-forms', ProtectAgainstSpam::class]);

// Nur Routen, die es im Controller mit View gibt (Route::resource registriert sonst auch leere Methoden).
// Erfasst wird bei diesen Bereichen direkt auf der Übersichtsseite, deshalb ohne create/show.
$crudOnIndex = ['index', 'store', 'edit', 'update', 'destroy'];

// Berechtigungen nach Stufe, siehe tests/README.md ("Berechtigungen") und App\Http\Middleware\Role.

// Team, Verwalter und Admin: Dashboard, Graphen, Buchungen ansehen, eigenes Profil
Route::group(['middleware' => 'role:team'], function () {
    Route::get('/admin', 'AdminController@index');
    Route::get('/admin/changes', ['as' => 'admin.changes', 'uses' => 'AdminController@changes']);
    Route::get('/admin/bookings', ['as' => 'admin.bookings', 'uses' => 'AdminController@bookings']);
    Route::get('admin/events', 'AdminEventController@index')->name('admin.events.index');
    Route::get('events/datatable', ['as' => 'events.CreateDataTablesTanStack', 'uses' => 'AdminEventController@createDataTablesTanStack']);
    // Nicht-Admins sehen und bearbeiten nur sich selbst (Prüfung im Controller)
    Route::resource('admin/users', 'AdminUserController')->only(['index', 'edit', 'update']);
    Route::get('admin/users/download/{user}', ['as' => 'download_signature', 'uses' => 'AdminUserController@get_signature']);
});

// Verwalter und Admin: Buchungen bearbeiten, Anfragen, Bewerbungen, Checkliste, Newsletter, Exporte
Route::group(['middleware' => 'role:verwalter'], function () use ($crudOnIndex) {
    Route::get('/admin/bookings/export-csv', ['as' => 'admin.exportcsv', 'uses' => 'AdminController@exportCSV']);
    // Bemerkung und Mail-Text auf der Buchungsseite (gespeichert in der Homepage)
    Route::patch('admin/homepages/{homepage}/comment_update', 'AdminHomepageController@commentUpdate')->name('admin.homepages.comment_update');
    Route::patch('admin/homepages/{homepage}/mail_text_update', 'AdminHomepageController@mailTextUpdate')->name('admin.homepages.mail_text_update');
    Route::resource('admin/events', 'AdminEventController')->only(['create', 'store', 'edit', 'update', 'destroy'])->names('admin.events');
    Route::get('admin/events/calendar/{event?}', [AdminEventController::class, 'calendar'])->name('events.calendar');
    Route::post('admin/events/{event}/SendCleaningMail', 'AdminEventController@SendCleaningMail')->name('events.sendCleaningMail');
    Route::post('admin/events/parse', [AdminEventController::class, 'parse'])->name('events.parse');
    Route::post('admin/events/createAPICode', [AdminEventController::class, 'createAPICode'])->name('events.createAPICode');

    Route::resource('admin/contacts', 'AdminContactController')->only(['index'])->names('admin.contacts');
    Route::post('contacts/{contact}/done', ['as' => 'contacts.done', 'uses' => 'AdminContactController@done']);
    Route::get('contacts/datatable', ['as' => 'contacts.CreateDataTablesTanStack', 'uses' => 'AdminContactController@createDataTablesTanStack']);
    Route::resource('admin/applications', 'AdminApplicationController')->only(['index', 'show']);
    Route::get('applications/datatable', ['as' => 'applications.CreateDataTablesTanStack', 'uses' => 'AdminApplicationController@createDataTablesTanStack']);
    Route::post('applications/{application}/refuse', ['as' => 'applications.refuse', 'uses' => 'AdminApplicationController@refuse']);
    Route::get('newsletter/datatable', ['as' => 'newsletter.CreateDataTablesTanStack', 'uses' => 'NewsletterController@createDataTablesTanStack']);
    Route::get('/admin/newsletter/export-bookings', ['as' => 'newsletter.exportBookings', 'uses' => 'NewsletterController@exportBookings']);
    Route::get('/admin/newsletter/export-members', ['as' => 'newsletter.exportMembers', 'uses' => 'NewsletterController@exportMembers']);
    Route::get('/admin/newsletter/import', ['as' => 'newsletter.import', 'uses' => 'NewsletterController@import']);
    Route::resource('admin/newsletter', 'NewsletterController')->only(['index', 'edit', 'update', 'destroy']);
    Route::resource('admin/checkpoints', 'AdminCheckpointController')->only($crudOnIndex);
    Route::resource('admin/rooms', 'AdminRoomsController')->only($crudOnIndex);
});

// Nur Admin: Inhalte der Webseite und Benutzerverwaltung
Route::group(['middleware' => 'role:admin'], function () use ($crudOnIndex) {
    Route::resource('admin/homepages', 'AdminHomepageController')->only(['index', 'edit', 'update']);
    Route::resource('admin/pictures', 'AdminPicturesController')->except(['show']);
    Route::resource('admin/testimonials', 'AdminTestimonialController')->only($crudOnIndex);
    Route::resource('admin/people', 'AdminPersonController')->only($crudOnIndex);
    Route::resource('admin/histories', 'AdminHistoryController')->only($crudOnIndex);
    Route::resource('admin/faqs', 'AdminFaqController')->only($crudOnIndex);
    Route::get('faqs/datatable', ['as' => 'faqs.CreateDataTablesTanStack', 'uses' => 'AdminFaqController@createDataTablesTanStack']);
    Route::resource('admin/faq_chapters', 'AdminFaqChapterController')->only($crudOnIndex);
    // create: Artikel aus Bexio abgleichen
    Route::resource('admin/positions', 'AdminPricelistPositionController')->only(['index', 'create', 'edit', 'update', 'destroy']);
    Route::resource('admin/users', 'AdminUserController')->only(['store', 'destroy']);

    // Vorschau der Mails an Buchende (nur rendern, kein Versand)
    Route::get('admin/mail-preview', 'MailPreviewController@index')->name('admin.mail-preview');
    Route::get('admin/mail-preview/{mail}', 'MailPreviewController@show')->name('admin.mail-preview.show');

    // Wartung: Aktionen nur per POST
    Route::get('admin/maintenance', 'MaintenanceController@index')->name('admin.maintenance');
    Route::post('admin/maintenance/migrate', 'MaintenanceController@migrate')->name('admin.maintenance.migrate');
    Route::post('admin/maintenance/clear-cache', 'MaintenanceController@clearCache')->name('admin.maintenance.clear-cache');
    Route::get('admin/bexio/connect', 'MaintenanceController@connectBexio')->name('admin.bexio.connect');
});

Route::get('/bexio/callback', function (Request $request, BexioAuthService $auth) {
    $state = (string) $request->session()->pull('bexio_oauth_state');
    abort_if($state === '' || ! hash_equals($state, (string) $request->get('state')), 403);

    $auth->exchangeAuthorizationCode($request->get('code'), route('bexio.callback'));

    return redirect()->route('admin.maintenance')->with('maintenance_output', 'Bexio erfolgreich verbunden.');
})->name('bexio.callback');
