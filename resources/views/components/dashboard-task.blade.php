@props(['task'])

<li>
    <a href="{{ $task['link'] }}" class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700">
        <span @class([
            'h-2.5 w-2.5 shrink-0 rounded-full',
            'bg-red-500' => $task['urgent'],
            'bg-amber-400' => ! $task['urgent'],
        ]) aria-hidden="true"></span>
        <span class="min-w-0 flex-1">
            <span class="block font-medium text-gray-900 dark:text-white">
                {{ $task['title'] }}
                @if ($task['urgent'])<span class="sr-only">(dringend)</span>@endif
            </span>
            <span class="block truncate text-xs text-gray-500 dark:text-gray-400">{{ $task['subtitle'] }}</span>
        </span>
        <span class="shrink-0 text-gray-400" aria-hidden="true">›</span>
    </a>
</li>
