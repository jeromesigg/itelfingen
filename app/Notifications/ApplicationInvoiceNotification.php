<?php

namespace App\Notifications;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Ixudra\Curl\Facades\Curl;
use App\Mail\ApplicationInvoiceMail;
use Illuminate\Notifications\Notification;

class ApplicationInvoiceNotification extends Notification
{
    use Queueable;

    public Application $application;
    public $invoice;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Application $application, $invoice)
    {
        //
        $this->application = $application;
        $this->invoice = $invoice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return new ApplicationInvoiceMail($this->application, $this->invoice);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $application = $this->application;

        return [
            'action' => 'Genossenschaft Rechnung versendet',
            'name' => $application->firstname.' '.$application->name,
            'bemerkung' => $application->comment,
        ];
    }
}
