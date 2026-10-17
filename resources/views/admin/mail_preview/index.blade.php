@extends('layouts.admin')

@section('content')
    <header>
        <h3 class="text-3xl font-bold dark:text-white">Mail-Vorschau</h3>
    </header>

    <p class="mt-2 text-gray-600 dark:text-gray-400">
        So sehen die Mails an Gäste aus. Es wird nichts versendet; Links zu Bexio und Anhänge fehlen in der Vorschau.
    </p>

    @php
        $exampleLabels = [
            'event' => fn ($event) => 'Buchung '.$event->number().' – '.$event->firstname.' '.$event->name.' (andere Buchung: ?event=ID)',
            'contact' => fn ($contact) => 'Neueste Kontaktanfrage – '.$contact->name,
            'application' => fn ($application) => 'Neueste Bewerbung – '.$application->firstname.' '.$application->name,
        ];
    @endphp

    @foreach (['event' => 'Buchungen', 'contact' => 'Kontakt', 'application' => 'Genossenschaft'] as $kind => $group)
        <h4 class="mt-6 text-xl font-semibold dark:text-white">{{ $group }}</h4>
        @if ($examples[$kind])
            <p class="text-sm text-gray-600 dark:text-gray-400">Beispiel: {{ $exampleLabels[$kind]($examples[$kind]) }}</p>
            <ul class="mt-2 space-y-1">
                @foreach ($mails as $mail => [$label, $mailKind])
                    @continue($mailKind !== $kind)
                    <li>
                        <a href="{{ route('admin.mail-preview.show', array_filter(['mail' => $mail, 'event' => $kind === 'event' ? $examples['event']->id : null])) }}" target="_blank" rel="noopener" class="text-orientalpink hover:underline">{{ $label }}</a>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="text-sm text-gray-600 dark:text-gray-400">Noch kein Datensatz vorhanden, mit dem die Vorschau gerendert werden kann.</p>
        @endif
    @endforeach
@endsection
