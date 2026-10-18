<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="tf-body">
        <header class="tf-topbar">
            <a href="{{ route('dashboard') }}" class="tf-brand" aria-label="TaskFlow, Dashboard">
                <img src="{{ asset('images/taskflow-logo.svg') }}" alt="TaskFlow" class="tf-brand-logo">
            </a>

            <form method="GET" action="{{ route('dashboard') }}" class="tf-global-search" role="search">
                <label class="sr-only" for="global-search">Rechercher un projet ou une tâche</label>
                <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.3"/><path d="m16 16 4 4"/></svg>
                <input id="global-search" type="search" name="search" value="{{ request('search') }}" placeholder="Rechercher un projet ou une tâche…">
                @if (request()->routeIs('dashboard'))
                    <input type="hidden" name="range" value="{{ request('range', 'month') }}">
                @endif
                <button type="submit" aria-label="Rechercher"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="10.8" cy="10.8" r="6.3"/><path d="m16 16 4 4"/></svg></button>
            </form>

            <div class="tf-topbar-actions">
                <a href="{{ route('notifications.index') }}" class="tf-notification-link" aria-label="Notifications, {{ $unreadNotificationsCount }} non lues" title="Notifications">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 9a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9M10 21h4"/></svg>
                    @if ($unreadNotificationsCount > 0)
                        <span class="tf-notification-count">{{ $unreadNotificationsCount > 99 ? '99+' : $unreadNotificationsCount }}</span>
                    @endif
                </a>
                <a href="{{ route('profile.edit') }}" class="tf-profile-link" title="Paramètres du profil">
                    @if (Auth::user()->profile_photo_path)
                        <img src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" class="tf-avatar tf-avatar-image profile-photo">
                    @else
                        <span class="tf-avatar" aria-hidden="true">{{ Auth::user()->initials }}</span>
                    @endif
                    <span class="tf-profile-name">{{ Auth::user()->name }}</span>
                    <svg class="tf-profile-chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m8 10 4 4 4-4"/></svg>
                </a>
                <form method="POST" action="{{ route('logout') }}" class="tf-logout-form">
                    @csrf
                    <button type="submit" aria-label="Se déconnecter" title="Se déconnecter">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M10 4H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h5M14 16l4-4-4-4m4 4H9"/></svg>
                    </button>
                </form>
            </div>
        </header>

        <aside class="tf-sidebar" aria-label="Navigation principale">
            <nav>
                <a href="{{ route('dashboard') }}" @class(['tf-nav-item', 'is-active' => request()->routeIs('dashboard')]) aria-label="Dashboard" title="Dashboard">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"/></svg>
                </a>
                <a href="{{ route('projects.index') }}" @class(['tf-nav-item', 'is-active' => request()->routeIs('projects.*')]) aria-label="Projets" title="Projets">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 7.5h7l2 2h9v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><path d="M3 9.5V6a2 2 0 0 1 2-2h4l2 2h5"/></svg>
                </a>
                <a href="{{ route('tasks.index') }}" @class(['tf-nav-item', 'is-active' => request()->routeIs('tasks.index') && ! request()->boolean('attachments')]) aria-label="Tâches" title="Tâches">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 6h11M9 12h11M9 18h11"/><circle cx="4.5" cy="6" r="1"/><circle cx="4.5" cy="12" r="1"/><circle cx="4.5" cy="18" r="1"/></svg>
                </a>
                <a href="{{ route('tasks.index', ['attachments' => 1]) }}" @class(['tf-nav-item', 'is-active' => request()->boolean('attachments')]) aria-label="Fichiers" title="Fichiers">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h8l5 5v13H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/><path d="M14 3v6h5"/></svg>
                </a>
                <a href="{{ route('profile.edit') }}" @class(['tf-nav-item', 'is-active' => request()->routeIs('profile.*')]) aria-label="Paramètres" title="Paramètres">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="m19.4 15 .1.1a1.7 1.7 0 1 1-2.4 2.4l-.1-.1a1.7 1.7 0 0 0-2.9 1.2v.2a1.7 1.7 0 1 1-3.4 0v-.2a1.7 1.7 0 0 0-2.9-1.2l-.1.1a1.7 1.7 0 1 1-2.4-2.4l.1-.1a1.7 1.7 0 0 0-1.2-2.9H4a1.7 1.7 0 1 1 0-3.4h.2a1.7 1.7 0 0 0 1.2-2.9l-.1-.1a1.7 1.7 0 1 1 2.4-2.4l.1.1a1.7 1.7 0 0 0 2.9-1.2V2a1.7 1.7 0 1 1 3.4 0v.2a1.7 1.7 0 0 0 2.9 1.2l.1-.1a1.7 1.7 0 1 1 2.4 2.4l-.1.1a1.7 1.7 0 0 0 1.2 2.9h.2a1.7 1.7 0 1 1 0 3.4h-.2a1.7 1.7 0 0 0-1.2 2.9z"/></svg>
                </a>
            </nav>
        </aside>

        <div class="tf-main-shell">
            @isset($header)
                <section class="tf-page-heading">{{ $header }}</section>
            @endisset
            <main>
                <x-flash-messages />
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
