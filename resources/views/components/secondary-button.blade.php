<button {{ $attributes->merge(['type' => 'button', 'class' => 'tf-btn tf-btn-secondary']) }}>
    {{ $slot }}
</button>
