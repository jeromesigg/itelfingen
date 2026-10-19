<?php

namespace App\Http\Controllers;

use App\Enums\ArchiveState;
use App\Enums\BookingState;
use App\Models\Event;
use App\Models\EventCheckpoint;
use App\Models\FaqChapter;
use App\Models\Homepage;
use App\Models\Person;
use App\Models\Picture;
use App\Models\PricelistPosition;
use App\Services\BexioApiService;
use App\Services\BookingDays;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        $homepage = Homepage::FindOrFail(1);
        $pictures = Picture::all();
        $positions = PricelistPosition::where([['show', true], ['archive_status_id', ArchiveState::Aktiv]])->orderby('bexio_code')->get();
        $events = Event::with('event_status')
            ->whereIn('event_status_id', BookingState::notCancelled())
            ->where('end_date', '>=', Carbon::now())
            ->orderBy('start_date')
            ->get();
        $booked_days = (new BookingDays(sundayIsFullDay: true))->markAll($events)->toArray();
        $discount = filter_var(config('app.discount_enabled'), FILTER_VALIDATE_BOOLEAN);
        $application_enabled = config('app.application_enabled');

        $contact_attributes = [
            'data-theme' => 'light',
            'data-callback' => 'enable_ContactBtn',
        ];
        $event_attributes = [
            'data-theme' => 'light',
            'data-callback' => 'enable_EventBtn',
        ];

        return view('home', compact('homepage', 'pictures', 'booked_days', 'event_attributes', 'contact_attributes', 'positions', 'discount', 'application_enabled'));
    }

    public function impressum()
    {
        $homepage = Homepage::FindOrFail(1);

        return view('contents.impressum', compact('homepage'));
    }

    public function about_us()
    {
        $homepage = Homepage::FindOrFail(1);
        $people = Person::where('archive_status_id', ArchiveState::Aktiv)->with('photo')->orderby('sort-index')->get();

        return view('contents.about_us', compact('homepage', 'people'));
    }

    public function faq()
    {
        $homepage = Homepage::FindOrFail(1);
        $title = 'FAQ';
        $faq_chapters = FaqChapter::where('archive_status_id', ArchiveState::Aktiv)
            ->whereHas('faqs')
            ->with(['photo', 'faqs' => fn ($query) => $query->orderBy('sort-index')->with('photo')])
            ->orderBy('sort-index')
            ->get();

        return view('contents.faq', compact('homepage', 'faq_chapters', 'title'));
    }

    public function bookings_login()
    {
        $homepage = Homepage::FindOrFail(1);
        $title = 'Login Kundenbereich';

        return view('contents.bookings_login', compact('homepage', 'title'));
    }

    public function bookings_uuid(string $uuid)
    {
        $homepage = Homepage::FindOrFail(1);
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $title = 'Deine Buchung Nr. '.str_pad($event['id'], 5, '0', STR_PAD_LEFT);
        $offerLink = $this->bexioDocumentLink('kb_offer', $event['bexio_offer_id']);
        $invoiceLink = $this->bexioDocumentLink('kb_invoice', $event['bexio_invoice_id']);

        return view('contents.bookings_show', compact('homepage', 'title', 'event', 'offerLink', 'invoiceLink'));
    }

    /**
     * Link auf Offerte oder Rechnung in Bexio für den Kundenbereich.
     * Ohne ID wird Bexio nicht gefragt; ist Bexio nicht erreichbar, erscheint die Seite ohne Link.
     */
    private function bexioDocumentLink(string $endpoint, int|string|null $id): ?string
    {
        if (empty($id)) {
            return null;
        }

        try {
            return app(BexioApiService::class)->get($endpoint.'/'.$id)['network_link'] ?? null;
        } catch (Throwable $e) {
            Log::warning('Bexio im Kundenbereich nicht erreichbar', ['endpoint' => $endpoint, 'id' => $id, 'error' => $e->getMessage()]);

            return null;
        }
    }

    public function bookings_check(Request $request)
    {
        $failed = fn () => redirect()->route('bookings.login')
            ->withErrors(['login' => 'Die eingegebenen Daten sind nicht korrekt.'])
            ->withInput($request->only('id'));

        if (! is_numeric($request->input('id')) || ! is_numeric($request->input('plz'))) {
            return $failed();
        }
        $event = Event::where('id', intval($request->input('id')))->where('plz', $request->input('plz'))->first();
        if ($event === null) {
            Log::warning('Fehlgeschlagener Booking-Login-Versuch', [
                'ip' => $request->ip(),
                'buchungsnummer' => $request->id,
                'user_agent' => $request->userAgent(),
            ]);

            return $failed();
        }

        return redirect()->route('bookings.uuid', ['uuid' => $event['uuid']]);
    }

    public function bookings_checklist(string $uuid)
    {
        $event = Event::where('uuid', $uuid)
            ->with(['event_rooms.room', 'event_rooms.event_checkpoints.checkpoint'])
            ->firstOrFail();
        $homepage = Homepage::FindOrFail(1);
        $title = 'Checkliste Hausabgabe';

        return view('contents.bookings_checklist', compact('homepage', 'title', 'event'));
    }

    public function DownloadLastInfos(string $uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        if ($event === null) {
            return redirect()->route('bookings.login')->withErrors('message', 'Die eingegebenen Daten sind nicht korrekt.');
        }
        $outputFile = Storage::disk('local')->path('contracts/Infos_vor_Buchung.pdf');

        return response()->download($outputFile);
    }

    public function bookings_checkpointDone(Request $request, string $uuid)
    {
        // Nur Checkpoints der Buchung mit dieser UUID, sonst könnte jeder fremde Checklisten abhaken.
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $checkpoint = EventCheckpoint::whereKey($request->get('checkpoint_id'))
            ->whereHas('event_room', fn ($query) => $query->where('event_id', $event->id))
            ->firstOrFail();
        $checkpoint->done = ! $checkpoint->done;
        $checkpoint->save();
        $event_room = $checkpoint->event_room;
        $done = ($event_room->event_checkpoints_open()->count() === 0);
        $event_room->done = $done;
        $event_room->save();

        return response()->json([
            'data' => [
                'checkpoint_done' => $checkpoint->done,
                'room_id' => $event_room->id,
                'room_done' => $event_room->done,
                'success' => true,
            ],
        ]);
    }
}
