@component('mail::message')
<h1>Kontaktformular</h1>
@component('mail::table')
    |        |          |
    | :------------- | :------------- |
    | Name  | {{$contact->name}} |
    | E-Mail | {{$contact->email}} |
    | Betreff | {{$contact->subject}} |
    | Text |  {{$contact->content}} |
@endcomponent
Vielen Dank für Ihre Anfrage, wir werden uns bald bei Ihnen melden.
@endcomponent
