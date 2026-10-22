<?php

namespace App\Console\Commands;

use App\Enums\BookingState;
use App\Helper\GlutzAPI;
use App\Models\Event;
use App\Notifications\EventFeedbackNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Notification;

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
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->SendFeedbackMails();
        $this->UpdateGlutzDevices();
    }

    public function SendFeedbackMails()
    {
        $date = Carbon::today()->addDays(1);
        $events = Event::where('feedback_mail', false)->where('end_date', '<=', $date)->where('event_status_id', '=', BookingState::Bestaetigt)->get();

        foreach ($events as $event) {
            Notification::send($event, new EventFeedbackNotification($event));
            $event->update(['feedback_mail' => true]);
        }
        if (count($events) > 0) {
            $this->info(count($events).' Feedback-Mails versendet.');
        }
    }

    public function UpdateGlutzDevices()
    {
        $result = GlutzAPI::updateDevice();
        $this->info('Glutz-Geräte aktualisiert. Ergebnis: '.json_encode($result));
    }
}
