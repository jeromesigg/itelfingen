<?php

namespace App\Console\Commands;

use App\Mail\WeeklyMail;
use App\Services\BookingChecks;
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
     * Execute the console command.
     *
     * @return int
     */
    public function handle(BookingChecks $checks)
    {
        Mail::send(new WeeklyMail($checks->forWeeklyMail(), $checks->openContacts()));
    }
}
