@component('mail::message')
    <h1>Deine Bewerbung für die Genossenschaft Ferienhaus Itelfingen</h1>
    @component('mail::table')
        |        |          |
        | :------------- | :------------- |
        | Name  | {{$application->firstname}} {{$application->name}} |
        | E-Mail | {{$application->email}} |
        | Anlass / Gruppe | {{$application->organisation}} |
        | Strasse |  {{$application->street}} |
        | PlZ / Ort | {{$application->plz}} {{$application->city}} |
        | Telefon | {{$application->telephone}} |
        | Wieso willst du Genossenschafter:in werden? | {{$application->why}} |
        | Bemerkung |  {{$application->comment}} |
    @endcomponent
    Sobald deine Bewerbung überprüft wurde, erhälst Du von uns Deine Rechnung für deinen Genossenschaftsschein über CHF 100.- per E-Mail.
@endcomponent
