@props(['type' => 'info', 'message' => null])

@php
    $classes = match($type) {
        'success' => 'bg-emerald-50 border-emerald-500 text-emerald-700',
        'error' => 'bg-rose-50 border-rose-500 text-rose-700',
        'warning' => 'bg-amber-50 border-amber-500 text-amber-700',
        default => 'bg-blue-50 border-blue-500 text-blue-700',
    };

    $icon = match($type) {
        'success' => '<svg class="h-5 w-5 text-emerald-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        'error' => '<svg class="h-5 w-5 text-rose-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        'warning' => '<svg class="h-5 w-5 text-amber-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>',
        default => '<svg class="h-5 w-5 text-blue-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
    };
@endphp

<div {{ $attributes->merge(['class' => "border-l-4 p-4 rounded shadow-sm flex items-start {$classes}"]) }} role="alert">
    <div class="flex-shrink-0 mt-0.5">
        {!! $icon !!}
    </div>
    <div>
        @if($message)
            <span class="font-medium">{{ $message }}</span>
        @else
            {{ $slot }}
        @endif
    </div>
</div>
