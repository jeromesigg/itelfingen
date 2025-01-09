<?php

namespace App\Http\Controllers;

use Notification;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Event;
use App\Helper\Helper;
use App\Models\Homepage;
use App\Models\Position;
use App\Models\Newsletter;
use App\Models\EventStatus;
use App\Events\EventCreated;
use Illuminate\Http\Request;
use App\Events\EventOfferSend;
use App\Models\ContractStatus;
use App\Events\EventInvoiceSend;
use App\Events\EventOfferCreate;
use App\Models\PricelistPosition;
use App\Events\EventInvoiceCreate;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\EventOfferSendNotification;
use Spatie\IcalendarGenerator\Components\Calendar;
use App\Notifications\EventInvoiceSendNotification;
use App\Notifications\EventCleaningSentNotification;
use App\Notifications\EventInvoiceCreatedNotification;
use App\Notifications\EventApplicationWantedNotification;
use Spatie\IcalendarGenerator\Components\Event as Event_ICAL;
use Spatie\IcalendarGenerator\Enums\EventStatus as EventStatus_ICAL;

class AdminEventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
    {
        //
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

        return view('admin.events.index', compact('event_type', 'events_json', 'positions', 'discount', 'contract_statuses', 'title'));
    }

    public function createDataTables(Request $request)
    {
        $input = $request->all();
        $contract_status = ContractStatus::where('name', '=', $input['status'])->first();
        $date = $input['date'] != 'Alle' ? Carbon::today() : null;
        $events = Event::when($contract_status, function ($query, $contract_status) {
            $query->where('contract_status_id', '=', $contract_status['id']);
        }, function ($query) {
            $query->where('contract_status_id', '<', config('status.contract_storniert'));
        })
            ->when($date, function ($query, $date) {
                $query->where('start_date', '>=', $date);
            })
            ->orderby('start_date')->get();

        return DataTables::of($events)
            ->addColumn('name', function (Event $event) {
                return $event['firstname'] . ' <a href='.\URL::route('admin.events.edit', $event).'>'.$event['name'].'</a>' .
                    '<br>' . $event['group_name'];
            })
            ->addColumn('number', function (Event $event) {
                return $event->number().'<br>'.$event['foreign_key'];
            })
            ->editColumn('start_date', function (Event $event) {
                return [
                    'display' => Carbon::parse($event['start_date'])->format('d.m.Y').' - '.
                        Carbon::parse($event['end_date'])->format('d.m.Y'),
                    'sort' => Carbon::parse($event['start_date'])->diffInDays('01.01.2021'),
                ];
            })
            ->editColumn('end_date', function (Event $event) {
                return [
                    'display' => Carbon::parse($event['end_date'])->format('d.m.Y'),
                    'sort' => Carbon::parse($event['end_date'])->diffInDays('01.01.2021'),
                ];
            })
            ->editColumn('user', function (Event $event) {
                return $event->user ? $event->user['username'] : '';
            })
            ->addColumn('status', function (Event $event) {
                return $event->status();
            })
            ->editColumn('contract_status', function (Event $event) {
                return $event->contract_status ? $event->contract_status['name'] : '';
            })
            ->rawColumns(['name', 'status', 'number'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        //
        $homepages = Homepage::all();
        $event_statuses = EventStatus::pluck('name', 'id')->all();
        $users = User::where('role_id', config('status.role_Verwalter'))->pluck('username', 'id')->all();
        $positions = PricelistPosition::where([['show', true], ['archive_status_id', config('status.aktiv')]])->orderby('bexio_code')->get();
        $title = 'Buchung erstellen';

        return view('admin.events.create', compact('event_statuses', 'homepages', 'users', 'positions', 'title'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        //
        $input = $request->all();
        $input['external'] = isset($input['foreign_key']);
        $input['contract_status_id'] = config('status.contract_offen');
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
        $input['uuid'] = \Illuminate\Support\Str::uuid();
        $event = Event::create($input);
        EventCreated::dispatch($event, $one_day, $input['positions']);
        if ($event['event_status_id'] == config('status.event_eigene')) {
            Helper::EventToGoogleCalendar($event);
        }

        return redirect()->route('events.edit', [$event]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
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
        $users = User::where('role_id', config('status.role_Verwalter'))->pluck('username', 'id')->all();
        $title = 'Buchung bearbeiten';

        return view('admin.events.edit', compact('event_statuses', 'event', 'contract_statuses', 'users', 'positions', 'title'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
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
                Position::where('id', $index)->update(['amount' => $plposition]);
            }
        }
        if ($input['contract_status_id'] == config('status.contract_storniert')) {
            $input['event_status_id'] = config('status.event_storniert');
        }
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

                    $event->update(['contract_status_id' => config('status.contract_angebot_versendet')]);
                }
                break;
            case '4':
                if (is_null($event['bexio_invoice_id']) && ! is_null($event['bexio_offer_id'])) {
                    EventInvoiceCreate::dispatch($event);
                    Notification::send($event, new EventInvoiceCreatedNotification($event, $additional_text));

                    $event->update([
                        'event_status_id' => config('status.event_bestaetigt'),
                        'contract_status_id' => config('status.contract_rechnung_erstellt'), ]);
                    
                    Newsletter::updateOrCreate(
                        ['email' => $event['email']],
                        [
                        'firstname' => $event['firstname'],
                        'name' => $event['name'],
                        'bookings' => true
                    ]);
                }
                break;
            case '5':
                if (isset($event['bexio_invoice_id'])) {
                    EventInvoiceSend::dispatch($event);
                    Notification::send($event, new EventInvoiceSendNotification($event, $additional_text));

                    $event->update([
                        'contract_status_id' => config('status.contract_rechnung_versendet'), ]);
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

    public function DownloadParking(Event $event)
    {
        $outputFile = Helper::PrintParking($event);

        return response()->download($outputFile);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {
        //
        $event = Event::findOrFail($id);
        if ($event->contract_signed) {
            unlink(public_path().$event->contract_signed->file);
        }
        $event->delete();

        return redirect('/admin/events');
    }

    public function api()
    {
        //
        $events = Event::where('end_date', '>', now())->where('event_status_id', '<', config('status.event_storniert'))->where('external', 'false')->get();
        $events_return = [];
        foreach ($events as $event) {
            $status = ($event['event_status_id'] === config('status.event_bestaetigt') || $event['event_status_id'] === config('status.event_eigene'));
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
        $events = Event::where('end_date', '>', now())->where('event_status_id', '<', config('status.event_storniert'))->get();
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
}
