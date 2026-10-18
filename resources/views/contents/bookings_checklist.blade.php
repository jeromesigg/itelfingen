@extends('layouts.app')

@php
  $checkpointTotal = $event->event_rooms->sum(fn ($eventRoom) => $eventRoom->event_checkpoints->count());
  $checkpointDone = $event->event_rooms->sum(fn ($eventRoom) => $eventRoom->event_checkpoints->where('done', true)->count());
  $checkIcon = 'M5 11.917 9.724 16.5 19 7.5';
@endphp

@section('content')
  @include('includes.header')
  <div id="app">
    <main id="main" class="bg-gray-50 dark:bg-gray-900">
      <div class="mx-auto max-w-3xl px-4 pt-10 pb-14 sm:pt-16">
        <a href="{{ route('bookings.uuid', $event->uuid) }}" class="inline-flex items-center gap-1 text-sm font-medium text-gray-600 hover:underline dark:text-gray-400">
          <span aria-hidden="true">‹</span> Zurück zu deiner Buchung
        </a>
        {{-- Titel und Fortschritt bleiben beim Scrollen unter dem Header sichtbar --}}
        <div class="sticky top-14 z-40 -mx-4 mt-2 border-b border-gray-200 bg-gray-50/95 px-4 py-3 backdrop-blur-sm sm:top-16 dark:border-gray-700 dark:bg-gray-900/95">
          <h1 class="font-body text-3xl text-gray-900 sm:text-4xl dark:text-white">Checkliste Hausabgabe</h1>
          @if ($checkpointTotal > 0)
            <div class="mt-2 flex items-center gap-3">
              <div class="h-2.5 flex-1 rounded-full bg-gray-200 dark:bg-gray-700">
                <div data-progress-bar class="h-2.5 rounded-full bg-gladegreen transition-all" style="width: {{ round($checkpointDone / $checkpointTotal * 100) }}%"></div>
              </div>
              <p class="shrink-0 text-sm font-medium text-gray-900 dark:text-white">
                <span data-progress-done>{{ $checkpointDone }}</span> von {{ $checkpointTotal }} erledigt
              </p>
            </div>
          @endif
        </div>
        <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">
          Hake erledigte Punkte ab – das wird sofort gespeichert. Ein Punkt lässt sich jederzeit wieder abwählen.
        </p>

        <div class="mt-8 space-y-4">
          @foreach ($event->event_rooms as $event_room)
            @php
              $roomTotal = $event_room->event_checkpoints->count();
              $roomDone = $event_room->event_checkpoints->where('done', true)->count();
            @endphp
            <details class="group rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800" data-room="{{ $event_room->id }}">
              <summary class="flex cursor-pointer list-none items-center gap-3 p-5 [&::-webkit-details-marker]:hidden">
                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full {{ $event_room->done ? 'bg-gladegreen text-white' : 'bg-gray-100 text-gray-400 dark:bg-gray-700' }}" data-room-icon>
                  <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $checkIcon }}" />
                  </svg>
                </span>
                <span class="flex-1">
                  <span class="block text-xl font-semibold text-gray-900 dark:text-white">{{ $event_room->room->name }}</span>
                  <span class="text-sm text-gray-600 dark:text-gray-400"><span data-room-done>{{ $roomDone }}</span> von {{ $roomTotal }} erledigt</span>
                </span>
                <svg class="h-5 w-5 shrink-0 text-gray-500 transition-transform group-open:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
                </svg>
              </summary>

              <ul class="grid gap-3 border-t border-gray-200 p-5 sm:grid-cols-2 dark:border-gray-700">
                @foreach ($event_room->event_checkpoints as $event_checkpoint)
                  <li>
                    <input type="checkbox" id="checkpoint-{{ $event_checkpoint->id }}" value="{{ $event_checkpoint->id }}" class="peer sr-only" data-checkpoint @checked($event_checkpoint->done)>
                    <label for="checkpoint-{{ $event_checkpoint->id }}"
                      class="flex h-full cursor-pointer gap-3 rounded-lg border-2 border-gray-200 bg-white p-4 text-gray-800 hover:bg-gray-50 peer-checked:border-gladegreen peer-checked:bg-green-50 peer-focus-visible:ring-4 peer-focus-visible:ring-gladegreen/40 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700 dark:peer-checked:bg-gray-700">
                      <span class="mt-0.5 flex h-6 w-6 shrink-0 items-center justify-center rounded-md border-2 border-gray-300 text-transparent [.peer:checked~label_&]:border-gladegreen [.peer:checked~label_&]:bg-gladegreen [.peer:checked~label_&]:text-white">
                        <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="{{ $checkIcon }}" />
                        </svg>
                      </span>
                      <span>
                        <span class="block font-semibold text-gray-900 dark:text-white">{{ $event_checkpoint->checkpoint->name }}</span>
                        <span class="mt-1 block text-sm [&_p]:mb-1">{!! $event_checkpoint->checkpoint->description !!}</span>
                      </span>
                    </label>
                  </li>
                @endforeach
              </ul>
            </details>
          @endforeach
        </div>

        <p id="checklist-error" class="mt-4 hidden rounded-lg bg-red-50 p-4 text-sm text-red-800" role="alert">
          Speichern hat nicht geklappt. Bitte prüfe deine Internetverbindung und versuche es nochmals.
        </p>
      </div>
    </main>
      @include('includes.footer')
  </div>
@endsection

@push('scripts')
<script>
  // Abhaken speichert sofort; danach Zähler und Raum-Status aktualisieren
  const checkboxes = document.querySelectorAll('[data-checkpoint]');

  function updateProgress() {
    document.querySelectorAll('[data-room]').forEach((room) => {
      const boxes = room.querySelectorAll('[data-checkpoint]');
      const done = room.querySelectorAll('[data-checkpoint]:checked').length;
      room.querySelector('[data-room-done]').textContent = done;
      const icon = room.querySelector('[data-room-icon]');
      const roomDone = boxes.length > 0 && done === boxes.length;
      icon.classList.toggle('bg-gladegreen', roomDone);
      icon.classList.toggle('text-white', roomDone);
      icon.classList.toggle('bg-gray-100', !roomDone);
      icon.classList.toggle('text-gray-400', !roomDone);
    });

    const total = checkboxes.length;
    const done = document.querySelectorAll('[data-checkpoint]:checked').length;
    const progressDone = document.querySelector('[data-progress-done]');
    if (progressDone) {
      progressDone.textContent = done;
      document.querySelector('[data-progress-bar]').style.width = Math.round(done / total * 100) + '%';
    }
  }

  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener('change', async () => {
      const error = document.getElementById('checklist-error');
      try {
        const response = await fetch('{{ route('bookings.checkpointDone', $event->uuid) }}', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()) },
          body: JSON.stringify({ checkpoint_id: checkbox.value }),
        });
        const data = await response.json();
        if (!response.ok || !data.data.success) {
          throw new Error('Speichern fehlgeschlagen');
        }
        checkbox.checked = Boolean(data.data.checkpoint_done);
        error.classList.add('hidden');
      } catch (e) {
        // Server hat nicht gespeichert: Häkchen zurücksetzen, damit die Anzeige stimmt
        checkbox.checked = !checkbox.checked;
        error.classList.remove('hidden');
      }
      updateProgress();
    });
  });
</script>
@endpush
