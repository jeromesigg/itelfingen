@component('mail::message')
# Die letzten Infos zu deinem Aufenthalt

@include('emails.events.partials.greeting')

Bald bist du im Ferienhaus Itelfingen. Hier das Wichtigste auf einen Blick:

@include('emails.events.partials.facts', ['extra' => [
    'Anreise' => 'ab '.($event->early_checkin ? config('house.checkin.early') : config('house.checkin.default')).' Uhr',
    'Abreise' => 'bis '.($event->late_checkout ? config('house.checkout.late') : config('house.checkout.default')).' Uhr',
]])

@if ($event->code)
@component('mail::panel')
**Türcode: {{ $event->code }}**
@endcomponent
@endif

- **Parkkarte:** Lege in jedes Auto eine Parkkarte (im Anhang), sonst droht eine Busse von CHF 240.–.
- **Nachtruhe:** ab {{ config('house.quiet_hours') }} Uhr.
- **Alles Weitere** steht im angehängten PDF und auf unserer FAQ-Seite.

@component('mail::button', ['url' => route('faq'), 'color' => 'success'])
Häufige Fragen ansehen
@endcomponent

@if ($additional_text)
{{ $additional_text }}

@endif
**Bei Notfällen** erreichst du uns direkt:

@foreach (config('house.emergency_contacts') as $contact)
- {{ $contact['name'] }}: {{ $contact['phone'] }}
@endforeach

Wenn du ein schönes Foto von deinem Aufenthalt teilst, freuen wir uns, wenn du uns auf [Instagram]({{ config('house.instagram_url') }}) markierst (@itelfingen) oder #ferienhausitelfingen verwendest.

@include('emails.events.partials.closing', ['step' => 4])
@endcomponent
