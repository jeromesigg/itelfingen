<?php

namespace App\Mail;

use Carbon\Carbon;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;

class MonthlyMail extends Mailable
{
    use Queueable, SerializesModels;

    protected Collection $events;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Collection $events)
    {
        $this->events = $events;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function build()
    {
        $month = Carbon::now()->locale('de')->addMonths(1)->monthName;
        return $this->markdown('emails.events.monthly', ['events' => $this->events, 'month' => $month])
            ->to(config('mail.from.address'), config('mail.from.name'))
            ->subject('Reinigungen im '. $month .' für das Ferienhaus Itelfingen');
    }
}
