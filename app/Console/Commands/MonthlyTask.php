<?php

namespace App\Console\Commands;

use App\Mail\MonthlyMail;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class MonthlyTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a task monthly';

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
        $date = Carbon::today()->addWeeks(6);
        $events = Event::where('start_date', '<=', $date)
            ->where('start_date', '>', Carbon::today())
            ->where(function ($query) {
                $query->where('event_status_id', '<>', config('status.event_storniert'))
                    ->orwhere('event_status_id', '=', config('status.event_storniert'))->where('cleaning_mail', true);
            })
            ->orderby('start_date')->get();

        Mail::send(new MonthlyMail($events));
    }
}
