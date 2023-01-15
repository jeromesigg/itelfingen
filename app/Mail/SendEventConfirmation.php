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

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Event  $event
     * @return void
     */
    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $email = $this->event['email'];
        $name = $this->event['firstname'].' '.$this->event['name'];

        $calendar = Calendar::create()
            ->productIdentifier('Itelfingen.ch')
            ->event(function (Event_ICAL $event) {
                $event->name('Email with iCal 101')
                    ->attendee('jerome.sigg@gmail.com')
                    ->startsAt(Carbon::parse('2022-12-15 08:00:00'))
                    ->endsAt(Carbon::parse('2022-12-19 17:00:00'))
                    ->fullDay()
                    ->address('Online - Google Meet');
            });
        $calendar->appendProperty(TextProperty::create('METHOD', 'REQUEST'));

        return $this->markdown('emails.events.confirmation', ['event' => $this->event])
            ->to($email, $name)
            ->cc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Deine Buchung für das Ferienhaus Itelfingen');
//            ->attachData($calendar->get(), 'invite.ics', [
//                'mime' => 'text/calendar; charset=UTF-8; method=REQUEST',
//            ]);
    }
}
