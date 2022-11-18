<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicationCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The event instance.
     *
     * @var \App\Models\Application
     */
    protected $application;

    /**
     * Create a new message instance.
     *
     * @param  \App\Models\Application  $application
     * @return void
     */
    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $application = $this->application;
        $name = $application['firstname'].' '.$application['name'];

        return $this->markdown('emails.applications.created', ['application' => $this->application])
            ->to($this->application['email'], $name)
            ->cc(config('mail.from.address'), config('mail.from.name'))
            ->subject('Deine Bewerbung für die Genossenschaft Ferienhaus Itelfingen');
    }
}
