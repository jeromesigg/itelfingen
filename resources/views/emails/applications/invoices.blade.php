@component('mail::message')
# Willkommen in der Genossenschaft

Hallo {{ $application->firstname ?: $application->name }}

Der Vorstand hat deine Bewerbung geprüft – wir freuen uns, dich in der Genossenschaft Ferienhaus Itelfingen willkommen zu heissen!

Mit der Bezahlung deines Genossenschaftsanteils von **CHF 100.–** wirst du Genossenschafter:in. Die Rechnung findest du im Anhang und online:

@component('mail::button', ['url' => $link, 'color' => 'success'])
Zur Rechnung
@endcomponent

Bitte bezahle sie bis **{{ $due_date }}** über eine der angegebenen Zahlungsmöglichkeiten.

@component('mail::panel')
**So buchst du zum Mitgliederpreis**<br>
Gib bei deiner Buchungsanfrage im Feld «Bemerkungen» deinen Namen an. Übernachten weitere Genossenschafter:innen mit, nenne auch ihre Namen.
@endcomponent

Bei Fragen sind wir gerne für dich da.

Freundliche Grüsse<br>
Der Vorstand<br>
Genossenschaft Ferienhaus Itelfingen
@endcomponent
