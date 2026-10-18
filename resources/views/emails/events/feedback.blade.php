@component('mail::message')
# {{ $heading }}

@include('emails.events.partials.greeting')

Wir hoffen, du hattest schöne Tage im Ferienhaus Itelfingen! Damit die Hausrückgabe reibungslos klappt:

- **Abreise** bis {{ $event->late_checkout ? config('house.checkout.late') : config('house.checkout.default') }} Uhr.
- **Checkliste:** Hake die Punkte für die Hausrückgabe online ab (Link unten).
- **Abfall** in die Container gemäss Hausordnung, der Code ist **{{ config('house.waste_code') }}**.
- **Mückennetze** bitte öffnen, damit sie bei einem Sturm nicht kaputtgehen.

@component('mail::button', ['url' => route('bookings.checklist', $event->uuid), 'color' => 'success'])
Zur Checkliste für die Hausrückgabe
@endcomponent

Hat sich bei der Anzahl Übernachtungen gegenüber dem Angebot etwas geändert? Dann melde es uns bitte, damit die Rechnung stimmt.

## Wie war's?

Mit deiner Rückmeldung hilfst du uns, noch besser zu werden.

@component('mail::button', ['url' => config('house.feedback_url'), 'color' => 'primary'])
Feedback geben
@endcomponent

Wir freuen uns, dich wieder einmal im Ferienhaus Itelfingen begrüssen zu dürfen. Gerne kannst du uns auch auf [Google]({{ config('house.google_review_url') }}) bewerten oder uns auf [Instagram]({{ config('house.instagram_url') }}) folgen.

@include('emails.events.partials.closing', ['step' => 5])
@endcomponent
