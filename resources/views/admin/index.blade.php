@extends('layouts.admin')

@php
$canManageBookings = Auth::user()->canManageBookings();
$statusBadges = [
App\Enums\BookingState::Neu->value => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
App\Enums\BookingState::Bestaetigt->value => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
App\Enums\BookingState::Eigene->value => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
];
$card = 'rounded-lg border border-gray-100 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800';
$cardTitle = 'px-4 pt-4 pb-2 text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400';
$visibleTasks = 5;
@endphp

@section('content')

<p class="mb-3 text-sm text-gray-500 dark:text-gray-400">{{ now()->locale('de_CH')->isoFormat('dddd, D. MMMM') }}</p>

<!-- Zähler -->
<div class="mb-4 grid grid-cols-3 gap-2 md:gap-4">
    @foreach ($counters as $counter)
    @php
    $tag = $counter->link ? 'a' : 'div';
    @endphp
    <{{ $tag }} @if($counter->link) href="{{ $counter->link }}" @endif class="{{ $card }} block p-3 md:p-4">
        <div class="text-2xl font-bold {{ $counter->color }}">{{ $counter->number }}</div>
        <div class="text-xs text-gray-600 dark:text-gray-300">{{ $counter->name }}</div>
    </{{ $tag }}>
    @endforeach
</div>

<div class="grid grid-cols-1 gap-4 xl:grid-cols-3">

    @if ($canManageBookings)
    <!-- Aufgaben -->
    <section class="{{ $card }} py-0">
        <h2 class="{{ $cardTitle }}">Aufgaben · {{ $tasks->count() }}</h2>
        @if ($tasks->isEmpty())
        <p class="px-4 pb-4 text-sm text-gray-600 dark:text-gray-300">Alles erledigt.</p>
        @else
        <ul class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach ($tasks->take($visibleTasks) as $task)
            <x-dashboard-task :task="$task" />
            @endforeach
        </ul>
        @if ($tasks->count() > $visibleTasks)
        <details class="group border-t border-gray-100 dark:border-gray-700">
            <summary
                class="cursor-pointer list-none px-4 py-3 text-center text-sm font-medium text-orientalpink group-open:hidden">
                + {{ $tasks->count() - $visibleTasks }} weitere anzeigen
            </summary>
            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                @foreach ($tasks->skip($visibleTasks) as $task)
                <x-dashboard-task :task="$task" />
                @endforeach
            </ul>
        </details>
        @endif
        @endif
    </section>
    @endif

    <!-- Nächste Aufenthalte -->
    <section class="{{ $card }} py-0">
        <h2 class="{{ $cardTitle }}">Nächste Aufenthalte</h2>
        <ul class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach ($events_current as $event)
            @php
            $start = Carbon\Carbon::parse($event->start_date);
            $end = Carbon\Carbon::parse($event->end_date);
            $days = (int) today()->diffInDays($start);
            if ($end->lt(today())) {
            [$badge, $badgeClass] = ['Vorbei', 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300'];
            } elseif ($start->lte(today())) {
            [$badge, $badgeClass] = ['Jetzt', 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300'];
            } else {
            [$badge, $badgeClass] = [$days === 1 ? 'Morgen' : "in $days T.", 'bg-blue-100 text-blue-800 dark:bg-blue-900
            dark:text-blue-300'];
            }
            $checks = $event->bookingState() === App\Enums\BookingState::Bestaetigt
            ? ['Infos' => $event->last_info, 'Putzmail' => $event->cleaning_mail, $event->code !== null ? $event->code :
            'Code' => $event->code !== null]
            : [];
            @endphp
            <li class="flex items-center gap-3 px-4 py-3">
                <span class="w-16 shrink-0 rounded px-1.5 py-0.5 text-center text-xs font-medium {{ $badgeClass }}">{{
                    $badge }}</span>
                <div class="min-w-0 flex-1">
                    @if ($canManageBookings)
                    <a href="{{ route('admin.events.edit', $event->id) }}"
                        class="block truncate font-medium text-gray-900 hover:text-orientalpink dark:text-white">{{
                        $event->displayName() }}</a>
                    @else
                    <span class="block truncate font-medium text-gray-900 dark:text-white">{{ $event->displayName()
                        }}</span>
                    @endif
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $start->format('d.m.') }}–{{ $end->format('d.m.Y') }}
                        @foreach ($checks as $label => $done)
                        · <span
                            class="{{ $done ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">{{
                            $done ? '✓' : '✗' }} {{ $label }}</span>
                        @endforeach
                    </div>
                </div>
                @if ($event->telephone)
                <a href="tel:{{ $event->telephone }}"
                    class="shrink-0 rounded-lg p-2 text-gray-500 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700"
                    aria-label="{{ $event->firstname }} {{ $event->name }} anrufen">
                    <i class="fa-solid fa-phone" aria-hidden="true"></i>
                </a>
                @endif
            </li>
            @endforeach
        </ul>
    </section>

    <!-- Neue Buchungen -->
    <section class="{{ $card }} py-0">
        <div class="flex items-center justify-between">
            <h2 class="{{ $cardTitle }}">Neue Buchungen</h2>
            <a href="{{ route('admin.events.index') }}"
                class="px-4 pt-4 pb-2 text-sm font-medium text-orientalpink">Alle ›</a>
        </div>
        <ul class="divide-y divide-gray-100 dark:divide-gray-700">
            @foreach ($events as $event)
            <li class="flex items-center gap-3 px-4 py-3">
                <div class="min-w-0 flex-1">
                    @if ($canManageBookings)
                    <a href="{{ route('admin.events.edit', $event->id) }}"
                        class="block truncate font-medium text-gray-900 hover:text-orientalpink dark:text-white">{{
                        $event->number() }} {{ $event->displayName() }}</a>
                    @else
                    <span class="block truncate font-medium text-gray-900 dark:text-white">{{ $event->number() }} {{
                        $event->displayName() }}</span>
                    @endif
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{
                        Carbon\Carbon::parse($event->start_date)->format('d.m.') }}–{{
                        Carbon\Carbon::parse($event->end_date)->format('d.m.Y') }}</div>
                </div>
                <span
                    class="shrink-0 rounded px-1.5 py-0.5 text-xs font-medium {{ $statusBadges[$event->bookingState()->value] ?? '' }}">{{
                    $event->event_status?->name }}</span>
            </li>
            @endforeach
        </ul>
    </section>
</div>
@endsection