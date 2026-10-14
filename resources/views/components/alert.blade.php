{{-- Meldungsbox (ersetzt Bootstraps "alert alert-*", Bootstrap ist nicht mehr eingebunden). type: danger | success --}}
@props(['type' => 'danger'])
@php
    $colors = [
        'danger' => 'text-red-800 bg-red-50 border-red-300 dark:bg-gray-800 dark:text-red-400 dark:border-red-800',
        'success' => 'text-fg-success-strong bg-success-soft border-success-subtle',
    ][$type];
@endphp
<div {{ $attributes->merge(['class' => "p-4 mb-4 text-sm border rounded-lg $colors", 'role' => 'alert']) }}>
    {{ $slot }}
</div>
