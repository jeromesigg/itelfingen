<?php

namespace App\Services;

use App\Models\Application;
use App\Models\Contact;
use App\Models\Event;
use App\Models\User;
use App\Notifications\AdminPushNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Throwable;

/**
 * Push-Meldungen bei neuen Buchungen, Kontaktanfragen und Bewerbungen an Verwalter und Admins,
 * die Meldungen auf mindestens einem Gerät eingeschaltet haben (Profil, Abschnitt "Push-Meldungen").
 */
class AdminPush
{
    public function eventCreated(Event $event): void
    {
        $dates = Carbon::parse($event->start_date)->format('d.m.').'–'.Carbon::parse($event->end_date)->format('d.m.Y');

        $this->send('Neue Buchungsanfrage', $event->displayName().' · '.$dates, route('admin.events.edit', $event->id));
    }

    public function contactCreated(Contact $contact): void
    {
        $this->send('Neue Kontaktanfrage', implode(' · ', array_filter([$contact->name, $contact->subject])), route('admin.contacts.index'));
    }

    public function applicationCreated(Application $application): void
    {
        $name = implode(' ', array_filter([$application->firstname, $application->name]));

        $this->send('Neue Bewerbung', $name, route('applications.show', $application->id));
    }

    /**
     * Ein Fehler beim Versand darf das Absenden eines öffentlichen Formulars nie verhindern.
     */
    private function send(string $title, string $body, string $url): void
    {
        $recipients = User::has('pushSubscriptions')->get()->filter(fn (User $user) => $user->canManageBookings());
        if ($recipients->isEmpty()) {
            return;
        }

        try {
            Notification::send($recipients, new AdminPushNotification($title, $body, $url));
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
