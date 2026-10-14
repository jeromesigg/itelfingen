<?php

namespace App\Http\Controllers;

use App\Enums\ArchiveState;
use App\Enums\BookingState;
use App\Enums\ContractState;
use App\Enums\UserRole;
use App\Events\EventCreated;
use App\Events\EventInvoiceCreate;
use App\Events\EventInvoiceSend;
use App\Events\EventOfferCreate;
use App\Events\EventOfferSend;
use App\Helper\GlutzAPI;
use App\Helper\Helper;
use App\Models\ContractStatus;
use App\Models\Event;
use App\Models\EventStatus;
use App\Models\Homepage;
use App\Models\Newsletter;
use App\Models\Position;
use App\Models\PricelistPosition;
use App\Models\User;
use App\Notifications\EventApplicationWantedNotification;
use App\Notifications\EventCleaningSentNotification;
use App\Notifications\EventInvoiceCreatedNotification;
use App\Notifications\EventInvoiceSendNotification;
use App\Notifications\EventOfferSendNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Str;
use Notification;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event as Event_ICAL;
use Spatie\IcalendarGenerator\Enums\EventStatus as EventStatus_ICAL;

class AdminEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Application|Factory|View
    {
        //
        $homepage = Homepage::FindOrFail(1);
        $event_type = 'admin';
        $events_all = Event::all();
        $positions = [];
        $discount = false;
        $contract_statuses = ContractStatus::pluck('name');

        $events_json = [];
        foreach ($events_all as $event) {
            $start_date = new Carbon($event['start_date']);
            $end_date = new Carbon($event['end_date']);
            $start = [
                'y' => $start_date->year,
                'm' => $start_date->month - 1,
                'd' => $start_date->day,
                'h' => ! $event['early_checkin'],
            ];
            $end = [
                'y' => $end_date->year,
                'm' => $end_date->month - 1,
                'd' => $end_date->day,
                'h' => ! $event['late_checkout'],
            ];
            $events_json[] = [
                'start' => $start,
                'end' => $end,
                'state' => $event->event_status['color'],
                'id' => $event->id,
            ];
        }
        $title = 'Buchungen';

        return view('admin.events.index', compact('event_type', 'events_json', 'positions', 'discount', 'contract_statuses', 'title', 'homepage'));
    }

    public function createDataTablesTanStack(Request $request)
    {
        // Filter aus der Tabelle; ohne Angabe: alle offenen Status, nur künftige Buchungen.
        $contract_status = ContractStatus::where('name', '=', $request->input('status'))->first();
        $date = $request->input('date') != 'Alle' ? Carbon::today() : null;
        $events = Event::when($contract_status, function ($query, $contract_status) {
            $query->where('contract_status_id', '=', $contract_status['id']);
        }, function ($query) {
            $query->whereIn('contract_status_id', ContractState::before(ContractState::Storniert));
        })
            ->when($date, function ($query, $date) {
                $query->where('start_date', '>=', $date);
            })
            ->orderby('start_date')
            ->get();

        // Werte aus dem öffentlichen Formular escapen: name, number und status werden als HTML gerendert
        // (siehe meta.html in App.vue), alle anderen Spalten als Text. Nur die Spalten der Tabelle,
        // keine internen Felder wie Türcode oder Bexio-IDs.
        $canEdit = $request->user()->canManageBookings();
        $data = $events->map(function (Event $event) use ($canEdit) {
            $name = e($event['name']);
            if ($canEdit) {
                $name = '<a class="text-orientalpink" href="'.route('admin.events.edit', $event).'">'.$name.'</a>';
            }

            return [
                'start_date' => [
                    'display' => Carbon::parse($event['start_date'])->format('d.m.Y').' - '.
                        Carbon::parse($event['end_date'])->format('d.m.Y'),
                    'sort' => Carbon::parse($event['start_date'])->diffInDays('01.01.2021'),
                ],
                'number' => $event->number().'<br>'.e($event['foreign_key']),
                'name' => e($event['firstname']).' '.$name.'<br>'.e($event['group_name']),
                'email' => $event['email'] ?? '',
                'total_amount' => $event['total_amount'] ?? '',
                'comment' => $event['comment'] ?? '',
                'comment_intern' => $event['comment_intern'] ?? '',
                'status' => $event->status(),
            ];
        });

        // TanStack-freundliches Response-Format
        return response()->json([
            'data' => $data,
            'rowCount' => count($data),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        //
        $homepages = Homepage::all();
        $event_statuses = EventStatus::pluck('name', 'id')->all();
        $users = User::where('role_id', UserRole::Verwalter)->pluck('username', 'id')->all();
        $positions = PricelistPosition::where([['show', true], ['archive_status_id', ArchiveState::Aktiv]])->orderby('bexio_code')->get();
        $title = 'Buchung erstellen';

        return view('admin.events.create', compact('event_statuses', 'homepages', 'users', 'positions', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Application|RedirectResponse|Redirector
     */
    public function store(Request $request)
    {
        //
        $input = $request->all();
        $input['external'] = isset($input['foreign_key']);
        $input['contract_status_id'] = ContractState::Offen;
        $one_day = false;
        if ($input['total_days'] < 1) {
            $input['start_date'] = new Carbon($input['start_date']);
            $input['end_date'] = new Carbon($input['start_date']);
            $input['total_days'] = 1;
            $one_day = true;
        } else {
            $input['start_date'] = new Carbon($input['start_date']);
            $input['end_date'] = new Carbon($input['end_date']);
        }
        $input['uuid'] = Str::uuid();
        $input['phonenumber_query'] = str_replace(' ', '', $input['telephone']);
        $input['phonenumber_query'] = str_replace('-', '', $input['phonenumber_query']);
        $event = Event::create($input);
        EventCreated::dispatch($event, $one_day, $input['positions']);
        if ($event->event_status_id === BookingState::Eigene) {
            Helper::EventToGoogleCalendar($event);
        }

        return redirect()->route('admin.events.edit', [$event]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Application|Factory|View
     */
    public function edit($id)
    {
        //
        $positions = Position::with('pricelist_position')
            ->where('event_id', $id)
            ->orderBy(PricelistPosition::select('bexio_code')
                ->whereColumn('pricelist_positions.id', 'positions.pricelist_position_id')
            )->get();

        $event_statuses = EventStatus::pluck('name', 'id')->all();
        $contract_statuses = ContractStatus::pluck('name', 'id')->all();
        $event = Event::findOrFail($id);
        $users = User::where('role_id', UserRole::Verwalter)->pluck('username', 'id')->all();
        $title = str_pad($event['id'], 5, '0', STR_PAD_LEFT).' - Buchung bearbeiten';

        return view('admin.events.edit', compact('event_statuses', 'event', 'contract_statuses', 'users', 'positions', 'title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return RedirectResponse
     */
    public function update(Request $request, Event $event)
    {
        //
        $input = $request->all();
        $input['external'] = isset($input['foreign_key']);
        $input['early_checkin'] = $request->has('early_checkin');
        $input['late_checkout'] = $request->has('late_checkout');
        if (isset($input['positions'])) {
            foreach ($input['positions'] as $index => $plposition) {
                // Nur Positionen dieser Buchung, die IDs kommen aus dem Formular.
                $event->positions()->whereKey($index)->update(['amount' => $plposition ?? 0]);
            }
        }
        if (ContractState::tryFrom((int) ($input['contract_status_id'] ?? 0)) === ContractState::Storniert) {
            $input['event_status_id'] = BookingState::Storniert;
        }
        $input['phonenumber_query'] = str_replace(' ', '', $input['telephone']);
        $input['phonenumber_query'] = str_replace('-', '', $input['phonenumber_query']);

        $event->update($input);
        $additional_text = $input['additional_text'] ?? '';

        switch (substr($input['submit'], 0, 1)) {
            case '2':
                EventOfferCreate::dispatch($event);
                break;
            case '3':
                if (! is_null($event['bexio_offer_id'])) {
                    EventOfferSend::dispatch($event);
                    Notification::send($event, new EventOfferSendNotification($event, $additional_text));

                    $event->update(['contract_status_id' => ContractState::AngebotVersendet]);
                }
                break;
            case '4':
                if (is_null($event['bexio_invoice_id']) && ! is_null($event['bexio_offer_id'])) {
                    EventInvoiceCreate::dispatch($event);
                    Notification::send($event, new EventInvoiceCreatedNotification($event, $additional_text));

                    $event->update([
                        'event_status_id' => BookingState::Bestaetigt,
                        'contract_status_id' => ContractState::RechnungErstellt, ]);

                    Newsletter::updateOrCreate(
                        ['email' => $event['email']],
                        [
                            'firstname' => $event['firstname'],
                            'name' => $event['name'],
                            'bookings' => true,
                        ]);
                }
                break;
            case '5':
                if (isset($event['bexio_invoice_id'])) {
                    EventInvoiceSend::dispatch($event);
                    Notification::send($event, new EventInvoiceSendNotification($event, $additional_text));

                    $event->update([
                        'contract_status_id' => ContractState::RechnungVersendet, ]);
                }
                break;
            case '6':
                Notification::send($event, new EventApplicationWantedNotification($event, $additional_text));
                break;
        }

        return redirect()->back();
    }

    public function SendCleaningMail(Request $request, Event $event)
    {
        $input = $request->all();
        Notification::send($event, new EventCleaningSentNotification($event, $input['cleaning_mail_address'], $input['cleaning_mail_text']));
        $event->update(['cleaning_mail' => true]);

        return redirect()->back();
    }

    public function DownloadParking(string $uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        if ($event === null) {
            return redirect()->route('bookings.login')->withErrors('message', 'Die eingegebenen Daten sind nicht korrekt.');
        }
        $outputFile = Helper::PrintParking($event);

        return response()->download($outputFile);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Application|RedirectResponse|Redirector
     */
    public function destroy($id)
    {
        //
        Event::findOrFail($id)->delete();

        return redirect('/admin/events');
    }

    public function api()
    {
        //
        $events = Event::where('end_date', '>', now())->whereIn('event_status_id', BookingState::notCancelled())->where('external', false)->get();
        $events_return = [];
        foreach ($events as $event) {
            $status = $event->bookingState()->isOccupied();
            $events_return[] = [
                'id' => $event['id'],
                'begins_at' => $event['start_date'],
                'ends_at' => $event['end_date'],
                'occupancy_type' => $status ? 'occupied' : 'tentative',
            ];
        }

        return $events_return;
    }

    public function api_ical()
    {
        //
        $calendar = Calendar::create(config('app.name'))
            ->withoutTimezone();
        $events = Event::where('end_date', '>', now())->whereIn('event_status_id', BookingState::notCancelled())->get();
        foreach ($events as $event) {
            $start = Carbon::parse($event->start_date);
            $end = Carbon::parse($event->end_date)->addDays(1);
            $calendar->event(Event_ICAL::create()
                ->startsAt($start)
                ->endsAt($end)
                ->status(EventStatus_ICAL::confirmed()));
        }

        return response($calendar->get(), 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="itelfingen.ics"',
        ]);
    }

    public function getPhoneNumber($api_token, $number)
    {
        if (preg_match('/^\+/', $number)) {
            $number = substr($number, 3);
        }
        // Nur Ziffern: % und _ wären sonst LIKE-Platzhalter. Zu kurze Nummern passen auf zu viele Buchungen.
        $number = preg_replace('/\D/', '', $number);
        if (strlen($number) < 6) {
            return response()->json(['error' => 'No event found with this number'], 404);
        }
        $event = Event::where('phonenumber_query', 'like', '%'.$number.'%')->get();
        //
        if ($event->isEmpty()) {
            return response()->json([
                'error' => 'No event found with this number',
            ], 404);
        } else {
            $event = $event->first();

            return response()->json([
                'number' => $event['telephone'],
                'firstname' => $event['firstname'],
                'name' => $event['name'],
                'date' => Carbon::parse($event['start_date'])->format('d.m.Y').' - '.Carbon::parse($event['end_date'])->format('d.m.Y'),
            ]);
        }
    }

    public function isFree($api_token, int $days)
    {
        if (! is_numeric($days) || $days < 1) {
            $days = 1;
        }
        $events = Event::where('start_date', '<=', Carbon::today()->addDays($days))->where('end_date', '>=', Carbon::today())->whereNotIn('event_status_id', [BookingState::Neu, BookingState::Storniert])->get();

        //
        return response()->json([
            'isFree' => $events->isEmpty(),
        ]);
    }

    public function parse(Request $request)
    {
        $request->validate(['text' => 'required|string']);

        $text = $request->input('text');

        $data = str_contains($text, 'Kommentar der Gruppe')
            ? Helper::parseFromMailText($text)
            : Helper::parseFromText($text);

        return response()->json($data);
    }

    public function createAPICode(Request $request)
    {
        $request->validate(['code' => 'required|string|size:4']);

        $code = $request->input('code');

        // Code überprüfen ob vom Event oder neuerstellt
        $event_id = $request->input('event_id');
        if (! is_numeric($event_id)) {
            return response()->json(['error' => 'Invalid event ID'], 400);
        }
        $event = Event::find($event_id);
        if (! $event) {
            return response()->json(['error' => 'Event not found'], 404);
        }
        $userName = 'Besucher '.$event->number();
        $userId = GlutzAPI::getUser($event, $userName, $code);
        if (isset($userId)) {

            $success = GlutzAPI::setCode($userId, $code);
            if (! $success) {

                return response()->json([
                    'error' => 'Code bereits in Verwendung für anderen User.',
                ], 500);
            } else {
                $accessPointId = GlutzAPI::getAccessPointId();
                if (isset($accessPointId)) {
                    if (GlutzAPI::getAccessPointOfUser($userId, $accessPointId)) {
                        GlutzAPI::setAccessPointsOfUser($event, $userId, $accessPointId);

                        $result = GlutzAPI::updateDevice();
                        if (empty($result['errorString'])) {
                            $event->update(['code' => $code]);

                            return response()->json([
                                'success' => 'Code erfolgreich erstellt.',
                                'User' => $userName,
                            ], 200);
                        } else {
                            return response()->json([
                                'error' => 'Fehler beim Update des Gerätes.',
                                'Meldung' => $result['errorString'] ?? 'Unbekannter Fehler',
                            ], 500);
                        }
                    } else {
                        return response()->json([
                            'error' => 'AccessPoint nicht gefunden.',
                            'User' => $userName,
                        ], 500);
                    }
                }
            }
        }

        // Glutz lieferte keinen Besucher oder keinen Zugangspunkt: JSON-Fehler statt leerer Antwort.
        return response()->json([
            'error' => 'Glutz nicht erreichbar oder unvollständige Antwort. Bitte später erneut versuchen.',
        ], 502);
    }
}
