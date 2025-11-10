<?php

namespace App\Console\Commands;

use Notification;
use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Console\Command;
use App\Notifications\EventFeedbackNotification;

class DailyEveningTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:daily-evening';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a task daily in the evening';

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
        $this->SendFeedbackMails();
    }

    public function SendFeedbackMails()
    {
        $date = Carbon::today()->addDays(1);
        $events = Event::where('feedback_mail', false)->where('end_date', '<=', $date)->where('event_status_id', '=', config('status.event_bestaetigt'))->get();

        foreach ($events as $event) {
            Notification::send($event, new EventFeedbackNotification($event));
            $event->update(['feedback_mail' => true]);
        }        if (count($events) > 0) {
            $this->info(count($events).' Feedback-Mails versendet.');
        }
    }
}
