{{-- Gemeinsamer Schluss aller Buchungs-Mails. Achtung Markdown: Zeilen nicht einrücken. $step: Nummer der Ablauf-Grafik (img/mail/{step}.png), optional --}}
Freundliche Grüsse<br>
Das Ferienhaus Itelfingen

---

Alle Infos und Dokumente zu deiner Buchung findest du jederzeit im Kundenbereich:

@component('mail::button', ['url' => route('bookings.uuid', $event->uuid), 'color' => 'primary'])
Deine Buchung ansehen
@endcomponent
@isset($step)
<div class="breadcrumbs">
<a href="{{ route('bookings.uuid', $event->uuid) }}">
<img src="https://itelfingen.ch/img/mail/{{ $step }}.png" style="display: block; margin: auto; max-width: 100%" width="100%" alt="Ablauf deiner Buchung"/>
</a>
</div>
@endisset
