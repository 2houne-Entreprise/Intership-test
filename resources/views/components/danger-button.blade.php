<button {{ $attributes->merge(['type' => 'submit', 'class' => 'tf-btn tf-btn-danger']) }}>
    {{ $slot }}
</button>
