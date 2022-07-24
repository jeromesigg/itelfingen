@component('mail::message')
    <h1>Reinigungsanfrage Ferienhaus Itelfingen</h1>
    @component('mail::table')
        {!! nl2br(e($text)) !!}
    @endcomponent
@endcomponent
