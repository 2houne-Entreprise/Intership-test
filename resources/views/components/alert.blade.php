@props(['type' => 'success'])

<div {{ $attributes->merge(['role' => $type === 'error' ? 'alert' : 'status'])->class([
    'p-4 rounded-lg border',
    'bg-green-50 border-green-200 text-green-800' => $type === 'success',
    'bg-red-50 border-red-200 text-red-800' => $type === 'error',
]) }}>
    {{ $slot }}
</div>
