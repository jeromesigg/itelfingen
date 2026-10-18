@php
$tones = [
    'success' => ['box' => 'border-green-200 bg-green-50 dark:border-green-800 dark:bg-green-950', 'icon' => 'text-green-600 dark:text-green-400', 'path' => 'M8.5 11.5 11 14l4-4m6 2a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
    'warning' => ['box' => 'border-yellow-200 bg-yellow-50 dark:border-yellow-800 dark:bg-yellow-950', 'icon' => 'text-yellow-600 dark:text-yellow-400', 'path' => 'M12 13V8m0 8h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
    'danger' => ['box' => 'border-red-200 bg-red-50 dark:border-red-800 dark:bg-red-950', 'icon' => 'text-red-600 dark:text-red-400', 'path' => 'm15 9-6 6m0-6 6 6m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
    'info' => ['box' => 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800', 'icon' => 'text-gray-500 dark:text-gray-400', 'path' => 'M12 8v4l3 3m6-3a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
][$tone];
@endphp
<div class="flex flex-col gap-4 rounded-lg border p-5 sm:flex-row sm:items-center {{ $tones['box'] }}">
    <svg class="h-8 w-8 shrink-0 {{ $tones['icon'] }}" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tones['path'] }}" />
    </svg>
    <div class="flex-1">
        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $title }}</p>
        <p class="text-gray-600 dark:text-gray-300">{{ $description }}</p>
    </div>
    @if ($action)
        <a href="{{ $action['link'] }}" target="_blank" rel="noopener"
            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-gladegreen px-5 py-2.5 text-sm font-medium text-white hover:opacity-90 focus:outline-hidden focus:ring-4 focus:ring-gladegreen/50">
            {{ $action['text'] }}
            <svg class="h-4 w-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 14v4.833A1.166 1.166 0 0 1 16.833 20H5.167A1.167 1.167 0 0 1 4 18.833V7.167A1.166 1.166 0 0 1 5.167 6h4.618m4.447-2H20v5.768m-7.889 2.121 7.778-7.778" />
            </svg>
        </a>
    @endif
</div>
