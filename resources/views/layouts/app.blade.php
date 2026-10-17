@extends('layouts.breeze')

@section('body')
    @if (session('status') || $errors->any())
        <div class="max-w-7xl mx-auto px-4 pt-6 sm:px-6 lg:px-8 space-y-4">
            @if (session('status'))
                <x-alert>{{ session('status') }}</x-alert>
            @endif
            @if ($errors->any())
                <x-alert type="error">
                    <p class="font-semibold">Veuillez corriger les erreurs suivantes :</p>
                    <ul class="list-disc ps-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif
        </div>
    @endif

    @yield('content')
@endsection
