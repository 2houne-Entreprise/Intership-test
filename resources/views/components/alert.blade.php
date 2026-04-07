@props(['type' => 'info', 'message'])

@php
    $classes = match($type) {
        'success' => 'bg-green-100 border-green-400 text-green-700',
        'error' => 'bg-red-100 border-red-400 text-red-700',
        'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
        default => 'bg-blue-100 border-blue-400 text-blue-700',
    };
@endphp

<div {{ $attributes->merge(['class' => 'border px-4 py-3 rounded ' . $classes]) }}>
    {{ $message }}
</div></content>
<parameter name="filePath">/home/boubacar/Documents/laravel/Intership-test/resources/views/components/alert.blade.php