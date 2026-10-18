<button {{ $attributes->merge(['type' => 'submit', 'class' => 'tf-btn tf-btn-primary']) }}>
    {{ $slot }}
</button>
