@extends('layouts.admin')

@section('content')
    <header>
        <h3 class="text-3xl font-bold dark:text-white">Mail-Vorschau</h3>
    </header>

    <p class="mt-2 text-gray-600 dark:text-gray-400">
        So sehen die Mails an Buchende aus. Es wird nichts versendet; Links zu Bexio und Anhänge fehlen in der Vorschau.
    </p>

    @if ($event)
        <p class="mt-4 dark:text-white">
            Beispiel-Buchung: <strong>{{ $event->number() }}</strong> – {{ $event->firstname }} {{ $event->name }}.
            Andere Buchung: <code>?event=ID</code> an die Adresse anhängen.
        </p>
        <ul class="mt-4 space-y-2">
            @foreach ($mails as $mail => $label)
                <li>
                    <a href="{{ route('admin.mail-preview.show', ['mail' => $mail, 'event' => $event->id]) }}" target="_blank" rel="noopener" class="text-orientalpink hover:underline">{{ $label }}</a>
                </li>
            @endforeach
        </ul>
    @else
        <p class="mt-4 dark:text-white">Es gibt noch keine Buchung, mit der die Vorschau gerendert werden kann.</p>
    @endif
@endsection
