@if (session('success') || $errors->any())
    <div {{ $attributes->merge(['class' => 'mx-auto mt-5 max-w-7xl space-y-3 px-4 sm:px-6 lg:px-8']) }}>
        @if (session('success'))
            <p role="status" class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">
                {{ session('success') }}
            </p>
        @endif

        @if ($errors->any())
            <div role="alert" class="rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                <ul class="list-inside list-disc space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
@endif