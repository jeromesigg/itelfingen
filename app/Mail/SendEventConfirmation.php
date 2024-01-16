<?php

namespace App\Mail;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event as Event_ICAL;
use Spatie\IcalendarGenerator\Properties\TextProperty;

class SendEventConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The event instance.
     *
     * @var \App\Models\Event
     */
    protected $event;
    protected $additional_text;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Event  $event
     * @return void
     */
    public function __construct(Event $event, string $additional_text)
    {
        $this->event = $event;
        $this->additional_text = $additional_text;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $event = $this->event;
        $email = $event['email'];
        $name = $event['firstname'].' '. $event['name'];
        $number = str_pad($this->event['id'],5,'0', STR_PAD_LEFT);
        if(isset($event['foreign_key'])){
            $number .= ' (' . $event['foreign_key'] . ')';
        }

        $calendar = Calendar::create(config('app.name'))
            ->productIdentifier('Itelfingen.ch')
            ->event(function (Event_ICAL $event_ical) use ($event, $email, $name) {
                $event_ical
                    ->name('Deine Buchung im Ferienhaus Itelfingen')
                    ->organizer(config('mail.from.address'), config('mail.from.name'))
                    ->attendee($email, $name)
                    ->startsAt(Carbon::parse($event->start_date))
                    ->endsAt(Carbon::parse($event->end_date)->addDay(1))
                    ->fullDay();
            });
        $calendar->appendProperty(TextProperty::create('METHOD', 'REQUEST'));

        return $this->markdown('emails.events.confirmation', ['event' => $event, 'additional_text' => $this->additional_text])
            ->to($email, $name)
            ->cc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Deine Buchung ' . $number . ' für das Ferienhaus Itelfingen')
            ->attachData($calendar->get(), 'invite.ics', [
                'mime' => 'text/calendar; charset=UTF-8; method=REQUEST',
            ]);
    }
}
