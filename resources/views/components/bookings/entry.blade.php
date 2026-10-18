@php
    // Drei Zustände: erledigt (grün), aktuell (nächster offener Schritt, hervorgehoben), offen (grau)
    [$circle, $titleColor] = match (true) {
        $fulfilled => ['bg-green-100 text-green-700 ring-gray-50 dark:bg-green-900 dark:text-green-300 dark:ring-gray-900', 'text-gray-900 dark:text-white'],
        $current => ['bg-gladegreen text-white ring-gladegreen/25', 'text-gray-900 dark:text-white'],
        default => ['bg-gray-100 text-gray-400 ring-gray-50 dark:bg-gray-800 dark:text-gray-500 dark:ring-gray-900', 'text-gray-500 dark:text-gray-400'],
    };
@endphp
<li class="mb-10 ms-6">
    <span class="absolute -inset-s-4 flex h-8 w-8 items-center justify-center rounded-full ring-4 {{ $circle }}">
        <svg class="h-5 w-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{$path}}" />
        </svg>
    </span>
    <h3 class="mb-0.5 flex flex-wrap items-center gap-2 pt-1 text-base font-semibold {{ $titleColor }}">
        {{$title}}
        @if($fulfilled)
            <span class="sr-only">(erledigt)</span>
        @elseif($current)
            <span class="rounded-full bg-gladegreen/15 px-2 py-0.5 text-xs font-medium text-gladegreen">Nächster Schritt</span>
        @endif
    </h3>
    @if($time)
        <time class="mb-2 block text-base font-normal text-gray-500 dark:text-gray-400"> {{\Carbon\Carbon::parse($time)->isoFormat('DD.MM.YY')}} </time>
    @endif
    @if($fulfilled)
        <x-bookings.link :event="$event" :type="$type" :contents="$contents" />
    @endif
</li>
