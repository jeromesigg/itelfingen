<?php

namespace App\Http\Controllers;

use App\Enums\BookingState;
use App\Models\Application;
use App\Models\Contact;
use App\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Mail\Markdown;
use Symfony\Component\HttpFoundation\Response;

/**
 * Vorschau der Mails an Gäste, Kontaktanfragen und Bewerbungen im Browser.
 * Rendert nur die Vorlagen: keine Bexio-Aufrufe, keine Anhänge, kein Versand.
 */
class MailPreviewController extends Controller
{
    /** @var array<string, array{0: string, 1: string}> Vorlage (unter emails.) => [Bezeichnung, Beispiel-Datensatz] */
    private const MAILS = [
        'events.created' => ['Buchungsanfrage eingegangen', 'event'],
        'events.offers' => ['Angebot', 'event'],
        'events.confirmation' => ['Buchung definitiv', 'event'],
        'events.last_infos' => ['Letzte Infos', 'event'],
        'events.feedback' => ['Abreise und Feedback', 'event'],
        'events.invoices' => ['Rechnung', 'event'],
        'events.application' => ['Interesse Genossenschaft', 'event'],
        'contacts.created' => ['Kontaktanfrage eingegangen', 'contact'],
        'applications.created' => ['Bewerbung eingegangen', 'application'],
        'applications.invoices' => ['Rechnung Genossenschaftsanteil', 'application'],
    ];

    public function index(Request $request): View
    {
        return view('admin.mail_preview.index', [
            'mails' => self::MAILS,
            'examples' => [
                'event' => $this->event($request),
                'contact' => Contact::latest('id')->first(),
                'application' => Application::latest('id')->first(),
            ],
        ]);
    }

    public function show(Request $request, string $mail): Response
    {
        abort_unless(array_key_exists($mail, self::MAILS), 404);

        $kind = self::MAILS[$mail][1];
        $model = match ($kind) {
            'event' => $this->event($request),
            'contact' => Contact::latest('id')->first(),
            'application' => Application::latest('id')->first(),
        };
        abort_if($model === null, 404, 'Kein Datensatz für die Vorschau vorhanden.');

        $html = app(Markdown::class)->render('emails.'.$mail, $this->exampleData($kind, $model));

        return response($html);
    }

    /**
     * Werte, die sonst aus Bexio oder dem Admin kommen.
     *
     * @return array<string, mixed>
     */
    private function exampleData(string $kind, Model $model): array
    {
        return [
            $kind => $model,
            'link' => '#bexio',
            'total' => 'CHF '.number_format((float) ($model->total_amount ?? 0), 2, '.', '’'),
            'valid_until' => now()->addMonth()->format('d.m.Y'),
            'due_date' => now()->addDays(30)->format('d.m.Y'),
            'heading' => 'Morgen ist dein Abreisetag',
            'additional_text' => 'Hier steht der zusätzliche Text aus dem Admin (falls ausgefüllt).',
        ];
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
