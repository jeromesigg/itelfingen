@component('mail::message')
# Deine Bewerbung ist eingegangen

Hallo {{ $application->firstname ?: $application->name }}

Vielen Dank für dein Interesse an der Genossenschaft Ferienhaus Itelfingen – wir freuen uns sehr! Hier nochmals deine Angaben:

@component('mail::table')
| Deine Bewerbung | |
| :------------ | :------------ |
| Name | {{ $application->firstname }} {{ $application->name }} |
@if ($application->organisation)
| Organisation | {{ $application->organisation }} |
@endif
| Adresse | {{ $application->street }} {{ $application->house_number }}, {{ $application->plz }} {{ $application->city }} |
| E-Mail | {{ $application->email }} |
@if ($application->telephone)
| Telefon | {{ $application->telephone }} |
@endif
@endcomponent

**Warum du Genossenschafter:in werden möchtest**<br>
{!! nl2br(e($application->why)) !!}

@if ($application->comment)
**Deine Fragen oder Bemerkungen**<br>
{!! nl2br(e($application->comment)) !!}

@endif
Mit deiner Bewerbung hast du die [Statuten]({{ url('/files/Statuten_Genossenschaft_Ferienhaus_Itelfingen.pdf') }}) der Genossenschaft akzeptiert.

**Wie geht es weiter?**<br>
Der Vorstand prüft deine Bewerbung. Spätestens nach zwei Wochen erhältst du per E-Mail die Rechnung für deinen Genossenschaftsanteil von CHF 100.–. Sobald sie bezahlt ist, bist du Genossenschafter:in.

Freundliche Grüsse<br>
Der Vorstand<br>
Genossenschaft Ferienhaus Itelfingen
@endcomponent
