<?php

namespace App\Console\Commands;

use App\Mail\WeeklyMail;
use App\Models\Contact;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class WeeklyTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:weekly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a task weekly';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $date = Carbon::today()->addMonths(4);
        $events_new = Event::where('contract_status_id', '<', config('status.contract_angebot_erstellt'))
            ->where('event_status_id', '<>', config('status.event_eigene'))
            ->orderby('start_date')->get();
        $events_open_offers = Event::where('start_date', '<=', $date)
            ->where('contract_status_id', '=', config('status.contract_angebot_erstellt'))
            ->orderby('start_date')->get();
        $events_no_cleaning_mail = Event::where('start_date', '<=', $date)
            ->where('start_date', '>', Carbon::today())
            ->where('cleaning_mail', false)
            ->where('event_status_id', '=', config('status.event_bestaetigt'))
            ->orderby('start_date')->get();
        $events_no_code = Event::where('start_date', '<=', $date)
            ->where('start_date', '>', Carbon::today())
            ->where('event_status_id', '=', config('status.event_bestaetigt'))
            ->whereNull('code')
            ->orderby('start_date')->get();
        $events_no_invoice = Event::where('end_date', '<', Carbon::today())
            ->where('event_status_id', '=', config('status.event_bestaetigt'))
            ->where('contract_status_id', '=', config('status.contract_rechnung_erstellt'))
            ->orderby('start_date')->get();
        $contacts_new = Contact::where('done', false)->get();

        $event_array = collect([
            ['text' => 'Folgende Buchungen wurden noch nicht bearbeitet', 'events' => $events_new],
            ['text' => 'Folgende Offerten wurden noch nicht angenommen', 'events' => $events_open_offers],
            ['text' => 'Folgende Buchungen haben noch kein Reinigungs-Mail versendet', 'events' => $events_no_cleaning_mail],
            ['text' => 'Folgende Buchungen haben noch keinen Tür-Code.', 'events' => $events_no_code],
            ['text' => 'Folgende Buchungen haben noch keine Rechnung erhalten.', 'events' => $events_no_invoice],
        ]);

        Mail::send(new WeeklyMail($event_array, $contacts_new));
    }
}
