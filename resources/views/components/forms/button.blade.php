@props(['variant' => 'primary'])
@php
    // danger für Löschen und andere unumkehrbare Aktionen
    $colors = $variant === 'danger'
        ? 'bg-red-700 hover:bg-red-800 focus:ring-red-300 dark:hover:bg-red-900'
        : 'bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 dark:hover:bg-blue-800';
@endphp
<button {{ $attributes->merge(['type' => 'button', 'class' => 'px-4 py-2 text-white rounded-md focus:outline-hidden focus:ring-2 transition-colors '.$colors]) }}>
    {{ $slot }}
</button>
