<?php

namespace App\Notifications;

use App\Mail\ApplicationCreated;
use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use jeremykenedy\Slack\Laravel\Facade as Slack;

class ApplicationCreatedNotification extends Notification
{
    use Queueable;

    public Application $application;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Application $application)
    {
        //
        $this->application = $application;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database', 'slack'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return new ApplicationCreated($this->application);
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
//        if (config('app.env') == 'production') {
        return (new SlackMessage)
            ->from(config('slack.username'), config('slack.icon'))
            ->to(config('slack.application_channel'))
            ->content('Es hat eine neue Bewerbung gegeben. '.$this->application['firstname'].' '.$this->application['name'].' würde gerne Genossenschafter/in werden. Grund: '.$this->application['why']);
//        }
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
            'action' => 'Genossenschaft Antrag',
            'name' => $application->firstname.' '.$application->name,
            'e-mail' => $application->email,
            'gruppe' => $application->organisation,
            'strasse' => $application->street,
            'ort' => $application->plz.' '.$application->city,
            'telefon' => $application->telephone,
            'wieso' => $application->why,
            'bemerkung' => $application->comment,
        ];
    }
}
