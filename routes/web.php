<?php

use App\Http\Controllers\AdminEventController;
use App\Services\BexioAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
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
Route::middleware('throttle:booking-login')->group(function () {
    Route::get('/bookings/login', 'HomeController@bookings_login')->name('bookings.login');
    Route::post('/bookings/login', 'HomeController@bookings_check')->name('bookings.check');
});
Route::get('bookings/{uuid}', 'HomeController@bookings_uuid')->name('bookings.uuid');
Route::get('bookings/{uuid}/checklist', 'HomeController@bookings_checklist')->name('bookings.checklist');
Route::get('admin/bookings/{uuid}/DownloadLastInfos', 'HomeController@DownloadLastInfos')->name('bookings.downloadLastInfos');
Route::get('admin/events/{uuid}/DownloadParking', 'AdminEventController@DownloadParking')->name('events.downloadParking');
Route::post('bookings/{uuid}/checkpoint/done', 'HomeController@bookings_checkpointDone')->name('bookings.checkpointDone');

Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');

// fullcalender
Route::post('event/create', ['as' => 'event.create', 'uses' => 'EventController@create'])->middleware(ProtectAgainstSpam::class);
Route::get('event/searchajaxcity', ['as' => 'searchajaxcity', 'uses' => 'EventController@searchResponseCity']);

Route::post('contacts', ['as' => 'contacts.store', 'uses' => 'ContactController@store'])->middleware(ProtectAgainstSpam::class);

// Berechtigungen nach Stufe, siehe tests/README.md ("Berechtigungen") und App\Http\Middleware\Role.

// Team, Verwalter und Admin: Dashboard, Graphen, Buchungen ansehen, eigenes Profil
Route::group(['middleware' => 'role:team'], function () {
    Route::get('/admin', 'AdminController@index');
    Route::get('/admin/changes', ['as' => 'admin.changes', 'uses' => 'AdminController@changes']);
    Route::get('/admin/bookings', ['as' => 'admin.bookings', 'uses' => 'AdminController@bookings']);
    Route::get('admin/events', 'AdminEventController@index')->name('admin.events.index');
    Route::get('events/createDataTables', ['as' => 'events.CreateDataTables', 'uses' => 'AdminEventController@createDataTables']);
    // Nicht-Admins sehen und bearbeiten nur sich selbst (Prüfung im Controller)
    Route::resource('admin/users', 'AdminUserController')->only(['index', 'edit', 'update']);
    Route::get('admin/users/download/{user}', ['as' => 'download_signature', 'uses' => 'AdminUserController@get_signature']);
});

// Verwalter und Admin: Buchungen bearbeiten, Anfragen, Bewerbungen, Checkliste, Newsletter, Exporte
Route::group(['middleware' => 'role:verwalter'], function () {
    Route::get('/admin/bookings/export-csv', ['as' => 'admin.exportcsv', 'uses' => 'AdminController@exportCSV']);
    // Bemerkung und Mail-Text auf der Buchungsseite (gespeichert in der Homepage)
    Route::patch('admin/homepages/{homepage}/comment_update', 'AdminHomepageController@CommentUpdate')->name('admin.homepages.comment_update');
    Route::patch('admin/homepages/{homepage}/mail_text_update', 'AdminHomepageController@MailTextUpdate')->name('admin.homepages.mail_text_update');
    Route::resource('admin/events', 'AdminEventController')->except(['index'])->names('admin.events');
    Route::post('admin/events/{event}/SendCleaningMail', 'AdminEventController@SendCleaningMail')->name('events.sendCleaningMail');
    Route::post('admin/events/parse', [AdminEventController::class, 'parse'])->name('events.parse');
    Route::post('admin/events/createAPICode', [AdminEventController::class, 'createAPICode'])->name('events.createAPICode');

    Route::resource('admin/contacts', 'AdminContactController')->names('admin.contacts');
    Route::post('contacts/{contact}/done', ['as' => 'contacts.done', 'uses' => 'AdminContactController@done']);
    Route::get('contacts/createDataTables', ['as' => 'contacts.CreateDataTables', 'uses' => 'AdminContactController@createDataTables']);
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

// Nur Admin: Inhalte der Webseite und Benutzerverwaltung
Route::group(['middleware' => 'role:admin'], function () {
    Route::resource('admin/homepages', 'AdminHomepageController');
    Route::resource('admin/pictures', 'AdminPicturesController');
    Route::resource('admin/testimonials', 'AdminTestimonialController');
    Route::resource('admin/people', 'AdminPersonController');
    Route::resource('admin/histories', 'AdminHistoryController');
    Route::resource('admin/faqs', 'AdminFaqController');
    Route::get('faqs/createDataTables', ['as' => 'faqs.CreateDataTables', 'uses' => 'AdminFaqController@createDataTables']);
    Route::resource('admin/faq_chapters', 'AdminFaqChapterController');
    Route::resource('admin/positions', 'AdminPricelistPositionController');
    Route::resource('admin/users', 'AdminUserController')->only(['store', 'destroy']);
});

Route::get('/bexio/connect', function (Request $request) {
    // state verknüpft den Callback mit dieser Weiterleitung, sonst könnte jemand ein fremdes Bexio-Konto verbinden.
    $state = Str::random(40);
    $request->session()->put('bexio_oauth_state', $state);

    $params = http_build_query([
        'client_id' => config('services.bexio.client_id'),
        'redirect_uri' => route('bexio.callback'),
        'response_type' => 'code',
        'state' => $state,
        'scope' => 'openid offline_access contact_edit kb_invoice_edit kb_offer_edit article_show article_show', // deine benötigten Scopes
    ]);

    return redirect("https://auth.bexio.com/realms/bexio/protocol/openid-connect/auth?{$params}");
})->middleware('auth.deploy');

Route::get('/bexio/callback', function (Request $request, BexioAuthService $auth) {
    $state = (string) $request->session()->pull('bexio_oauth_state');
    abort_if($state === '' || ! hash_equals($state, (string) $request->get('state')), 403);

    $auth->exchangeAuthorizationCode($request->get('code'), route('bexio.callback'));

    return 'Bexio erfolgreich verbunden!';
})->name('bexio.callback');

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

Route::get('/deploy', function () {
    // Migrationen erzwingen
    Artisan::call('migrate', ['--force' => true]);

    // Optional: Cache-Handling
    Artisan::call('optimize:clear');

    return response()->json([
        'status' => 'ok',
        'message' => 'Deployment erfolgreich durchgeführt 🚀',
    ]);
})->middleware('auth.deploy');
