<?php

namespace App\Providers;

use App\Events\ApplicationCreatedEvent;
use App\Events\EventCreated;
use App\Events\EventInvoiceCreate;
use App\Events\EventInvoiceSend;
use App\Events\EventOfferCreate;
use App\Events\EventOfferSend;
use App\Listeners\ApplicationCreatedListener;
use App\Listeners\EventContactCreateListener;
use App\Listeners\EventCreatedListener;
use App\Listeners\EventInvoiceCreateListener;
use App\Listeners\EventInvoiceSendListener;
use App\Listeners\EventOfferCreateListener;
use App\Listeners\EventOfferSendListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Mail\Events\MessageSending;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        EventCreated::class => [
            EventCreatedListener::class,
        ],
        EventOfferCreate::class => [
            EventContactCreateListener::class,
            EventOfferCreateListener::class,
        ],
        EventOfferSend::class => [
            EventOfferSendListener::class,
        ],
        EventInvoiceCreate::class => [
            EventInvoiceCreateListener::class,
        ],
        EventInvoiceSend::class => [
            EventInvoiceSendListener::class,
        ],
        ApplicationCreatedEvent::class => [
            ApplicationCreatedListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
