<?php

namespace App\Http\Controllers;

use App\Enums\BookingState;
use App\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vorschau der Mails an Buchende im Browser. Rendert nur die Vorlagen:
 * keine Bexio-Aufrufe, keine Anhänge, kein Versand.
 */
class MailPreviewController extends Controller
{
    /** @var array<string, string> Vorlage => Bezeichnung, in der Reihenfolge des Ablaufs */
    private const MAILS = [
        'created' => 'Buchungsanfrage eingegangen',
        'offers' => 'Angebot',
        'confirmation' => 'Buchung definitiv',
        'last_infos' => 'Letzte Infos',
        'feedback' => 'Abreise und Feedback',
        'invoices' => 'Rechnung',
        'application' => 'Interesse Genossenschaft',
    ];

    public function index(Request $request): View
    {
        return view('admin.mail_preview.index', [
            'mails' => self::MAILS,
            'event' => $this->event($request),
        ]);
    }

    public function show(Request $request, string $mail): Response
    {
        abort_unless(array_key_exists($mail, self::MAILS), 404);

        $event = $this->event($request);
        abort_if($event === null, 404, 'Keine Buchung für die Vorschau vorhanden.');

        $html = app(Markdown::class)->render('emails.events.'.$mail, [
            'event' => $event,
            'link' => '#bexio',
            'total' => 'CHF '.number_format((float) $event->total_amount, 2, '.', '’'),
            'valid_until' => now()->addMonth()->format('d.m.Y'),
            'heading' => 'Morgen ist dein Abreisetag',
            'additional_text' => 'Hier steht der zusätzliche Text aus dem Admin (falls ausgefüllt).',
        ]);

        return response($html);
    }

    /** Buchung aus ?event=ID, sonst die neueste bestätigte */
    private function event(Request $request): ?Event
    {
        if ($request->filled('event')) {
            return Event::find($request->integer('event'));
        }

        return Event::where('event_status_id', BookingState::Bestaetigt)->latest('id')->first()
            ?? Event::latest('id')->first();
    }
}
