<?php

namespace App\Console\Commands;

use App\Contact;
use App\Event;
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
        $date = Carbon::today()->addMonths(2);
        $events_new = Event::where('event_status_id','=', config('status.event_neu'))->get();
        $events_open_offers = Event::where('start_date','<=', $date )->where('contract_status_id','<=', config('status.contract_angebot_versendet'))->get();
        $events_no_cleaning_mail = Event::where('start_date','<=', $date )->where('cleaning_mail',false)->get();
        $contacts_new = Contact::where('done',false)->get();

        $data["email"] = "jerome.sigg@gmail.com";
        $data["title"] = "Wöchentliches Errinerungsmail";

        // $this->info($data);
        // return $data;
  
        Mail::send('emails.weekly_reminder', compact('data', 'contacts_new', 'events_new', 'events_open_offers', 'events_no_cleaning_mail'), function($message)use($data) {
            $message->to($data["email"], $data["email"])
                    ->subject($data["title"]);
        });

    }
}
