<?php

namespace App\Console\Commands;

use DateTime;
use App\Event;
use App\Contact;
use App\Helper\Helper;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event as Event_ICS;

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
        $date = Carbon::today()->addMonths(2);
        $events_new = Event::where('event_status_id','=', config('status.event_neu'))->get();
        $events_open_offers = Event::where('start_date','<=', $date )
            ->where('contract_status_id','<=', config('status.contract_angebot_versendet'))
            ->where('event_status_id','<=', config('status.event_bestaetigt'))->get();
        $events_no_cleaning_mail = Event::where('start_date','<=', $date )
            ->where('cleaning_mail',false)->get();
        $contacts_new = Contact::where('done',false)->get();

        $event_array =collect([
            ['text' => "Folgende Buchungen wurden noch nicht bearbeitet",'events'=>$events_new], 
            ['text' => "Folgende Offerten wurden noch nicht angenommen",'events'=>$events_open_offers], 
            ['text' => "Folgende Buchungen haben noch kein Reiningungs-Mail versendet",'events'=>$events_no_cleaning_mail]
        ]);

 

        $data["title"] = "Wöchentliches Errinerungsmail";
          
        Mail::send('emails.weekly_reminder', compact('data', 'contacts_new', 'events_new', 'events_open_offers', 'events_no_cleaning_mail'), function($message)use($data) {
            $message->to(config('mail.from.address'), config('mail.from.name'))
                    ->subject($data["title"]);
        });

    }
}
