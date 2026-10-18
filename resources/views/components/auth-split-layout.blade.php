<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>TaskFlow · Connexion</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="tf-auth-body">
        <div class="tf-auth-page">
            <aside class="tf-auth-story" aria-label="Découvrez TaskFlow">
                <a href="{{ route('login') }}" class="tf-auth-story-logo" aria-label="TaskFlow, accueil connexion">
                    <img src="{{ asset('images/taskflow-logo.svg') }}" alt="TaskFlow">
                </a>

                <div class="tf-auth-story-copy">
                    <h1>Organisez vos projets<br>et suivez vos tâches <span>simplement.</span></h1>
                    <p>TaskFlow vous aide à centraliser vos projets, collaborer avec votre équipe et garder le cap sur vos objectifs.</p>
                </div>

                <div class="tf-auth-preview" aria-hidden="true">
                    <div class="tf-auth-preview-browserbar">
                        <span></span><span></span><span></span>
                        <div class="tf-auth-preview-search"><i></i>Rechercher un projet ou une tâche...</div>
                        <b>B</b>
                    </div>
                    <div class="tf-auth-preview-app">
                        <nav class="tf-auth-preview-nav">
                            <img src="{{ asset('images/taskflow-logo.svg') }}" alt="">
                            <span>Tableau de bord</span>
                            <strong>Mes projets</strong>
                            <span>Mes tâches</span>
                            <span>Fichiers</span>
                            <span>Paramètres</span>
                        </nav>
                        <div class="tf-auth-preview-content">
                            <div class="tf-auth-preview-heading"><strong>Mes projets</strong><span>＋ Nouveau projet</span></div>
                            <article class="tf-auth-preview-project">
                                <i class="tf-auth-preview-folder" aria-hidden="true"></i>
                                <div><strong>StudyTime</strong><small>Application permettant d'organiser mes études, de planifier les tâches et de suivre leur progression.</small></div>
                                <div class="tf-auth-preview-members"><b>A</b><b>M</b><small>2 membres</small></div>
                            </article>
                        </div>
                    </div>
                </div>
                <span class="tf-auth-story-leaf tf-auth-story-leaf-one" aria-hidden="true"></span>
                <span class="tf-auth-story-leaf tf-auth-story-leaf-two" aria-hidden="true"></span>
            </aside>

            <main class="tf-auth-main">
                <div class="tf-auth-panel">
                    <a href="{{ route('login') }}" class="tf-auth-panel-logo" aria-label="TaskFlow">
                        <img src="{{ asset('images/taskflow-logo.svg') }}" alt="TaskFlow">
                    </a>
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>