<?php

namespace App\Http\Controllers;

use App\Events\EventCreated;
use App\Events\EventInvoiceCreate;
use App\Events\EventInvoiceSend;
use App\Events\EventOfferCreate;
use App\Events\EventOfferSend;
use App\Helper\Helper;
use App\Mail\CleaningSent;
use App\Models\ContractStatus;
use App\Models\Event;
use App\Models\EventStatus;
use App\Models\Homepage;
use App\Models\Position;
use App\Models\PricelistPosition;
use App\Models\User;
use App\Notifications\EventInvoiceCreatedNotification;
use App\Notifications\EventInvoiceSendNotification;
use App\Notifications\EventOfferSendNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Notification;
use Yajra\DataTables\Facades\DataTables;

class AdminEventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
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
                'h' => true,
            ];
            $end = [
                'y' => $end_date->year,
                'm' => $end_date->month - 1,
                'd' => $end_date->day,
                'h' => true,
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
                return '<a href='.\URL::route('events.edit', $event).'>'.$event['name'].'</a>';
            })
            ->addColumn('number', function (Event $event) {
                return str_pad($event['id'],5,'0', STR_PAD_LEFT);
            })
            ->editColumn('start_date', function (Event $event) {
                return [
                    'display' => Carbon::parse($event['start_date'])->format('d.m.Y'),
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
            ->editColumn('event_status', function (Event $event) {
                return $event->event_status ? $event->event_status['name'] : '';
            })
            ->editColumn('contract_status', function (Event $event) {
                return $event->contract_status ? $event->contract_status['name'] : '';
            })
            ->rawColumns(['name'])
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

        return view('admin.events.create', compact('event_statuses', 'homepages', 'users', 'positions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        //
        $input = $request->all();
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
        $event = Event::create($input);
        EventCreated::dispatch($event, $one_day, $input['positions']);
        if ($event['event_status_id'] == config('status.event_eigene')) {
            Helper::EventToGoogleCalendar($event);
        }

        return redirect('/admin/events');
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

        return view('admin.events.edit', compact('event_statuses', 'event', 'contract_statuses', 'users', 'positions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        //
        $event = Event::findOrFail($id);
        $input = $request->all();
        $input['external'] = $request->has('external');
        if (isset($input['positions'])) {
            foreach ($input['positions'] as $index => $plposition) {
                Position::where('id', $index)->update(['amount' => $plposition]);
            }
        }
        $event->update($input);

        return redirect()->back();
    }

    public function CreateOffer(Event $event)
    {
        EventOfferCreate::dispatch($event);

        return redirect()->back();
    }

    public function SendOffer(Event $event)
    {
        if (! is_null($event['bexio_offer_id'])) {
            EventOfferSend::dispatch($event);
            Notification::send($event, new EventOfferSendNotification($event));

            $event->update(['contract_status_id' => config('status.contract_angebot_versendet')]);
        }

        return redirect()->back();
    }

    public function CreateInvoice(Event $event)
    {
        if (is_null($event['bexio_invoice_id']) && ! is_null($event['bexio_offer_id'])) {
            EventInvoiceCreate::dispatch($event);
            Notification::send($event, new EventInvoiceCreatedNotification($event));

            $event->update([
                'event_status_id' => config('status.event_bestaetigt'),
                'contract_status_id' => config('status.contract_rechnung_erstellt'), ]);
        }

        return redirect()->back();
    }

    public function SendInvoice(Event $event)
    {
        if (isset($event['bexio_invoice_id'])) {
            EventInvoiceSend::dispatch($event);
            Notification::send($event, new EventInvoiceSendNotification($event));

            $event->update([
                'contract_status_id' => config('status.contract_rechnung_versendet'), ]);
        }

        return redirect()->back();
    }

    public function SendCleaningMail(Request $request, Event $event)
    {
        $input = $request->all();
        Mail::send(new CleaningSent($event, $input['cleaning_mail_address'], $input['cleaning_mail_text']));
        $event->update(['cleaning_mail' => true]);

        return redirect()->back();
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
        $events = Event::where('end_date','>',now())->where('event_status_id','<',config('status.event_storniert'))->where('external','false')->get();
        $events_return = [];
        foreach ($events as $event)
        {
            $status = ($event['event_status_id']===config('status.event_bestaetigt') || $event['event_status_id']===config('status.event_eigene'));
            $events_return[] = [
                'id' => $event['id'],
                'begins_at' => $event['start_date'],
                'ends_at' => $event['end_date'],
                'occupancy_type' => $status ? 'occupied' : 'tentative',
            ];
        }
        return $events_return;
    }
}
