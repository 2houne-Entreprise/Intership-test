# TaskFlow

Application Laravel 13 de gestion de projets et de tâches. Chaque utilisateur gère ses propres projets, leurs tâches, les statuts, les échéances et les pièces jointes.

## Prérequis

- PHP 8.4 minimum, Composer 2 et les extensions Laravel usuelles, notamment `pdo_sqlite`, `mbstring`, `fileinfo`, `openssl` et `curl`.
- Node.js 22.12+ (ou 20.19+) et npm, conformément à Vite 8.
- SQLite : aucun serveur de base de données à installer.
- Accès en écriture à `storage` et `bootstrap/cache`.

Le sujet cible PHP 8.4. Les vérifications locales ont été réalisées avec PHP 8.5.11 ; une exécution sous PHP 8.4 reste à confirmer avant remise.

## Installation

Depuis la racine du dépôt :

```sh
composer install
php -r "file_exists('.env') || copy('.env.example', '.env');"
php artisan key:generate
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
php artisan migrate --no-interaction
php artisan storage:link --no-interaction
npm ci --ignore-scripts
npm run build
php artisan serve
```

Ouvrir l’adresse affichée par Artisan, puis s’inscrire. Après connexion, le menu **Mes projets** permet de créer un projet et d’accéder à ses tâches. Aucun compte précréé n’est nécessaire.

Le fichier `.env.example` utilise `DB_CONNECTION=sqlite`. En l’absence de `DB_DATABASE`, Laravel choisit `database/database.sqlite`. Adapter `APP_URL` à l’adresse utilisée. Le fichier `.env`, sa clé `APP_KEY`, la base locale et les fichiers téléversés ne doivent pas être commités.

Pour développer avec rechargement des assets, remplacer le build par `npm run dev` dans un second terminal. `composer run dev` démarre également le serveur, Vite et le worker de queue. Si les styles ne reflètent pas les changements, relancer `npm run build` ou garder Vite actif.

Les e-mails de réinitialisation du mot de passe sont écrits dans les logs avec `MAIL_MAILER=log`. Aucun service SMTP n’est nécessaire en local.

## Fonctionnalités et organisation

- **US1** : authentification Breeze avec Blade, inscription, connexion et déconnexion.
- **US2** : CRUD projets ; routes dans `routes/projects.php`, chargées sous `web` et `auth` depuis `bootstrap/app.php`.
- **US3** : tâches avec titre, statut obligatoire et échéance facultative ; `StoreTaskRequest` et `UpdateTaskRequest` conservent les erreurs et les anciennes saisies.
- **US4** : `layouts/app.blade.php` étend la base Breeze avec `@yield('content')`, composant `x-alert`, tableau et changement rapide du statut.
- **US5** : `EnsureUserOwnsProject` retourne 403 pour un projet appartenant à un autre utilisateur. Les policies complètent ce contrôle. Les routes imbriquées vérifient aussi que la tâche appartient au projet, sinon elles retournent 404.
- **US6** : `Task::overdue()` sélectionne les tâches non terminées dont l’échéance est antérieure à aujourd’hui. Les tâches sans échéance ou dues aujourd’hui sont exclues. `status_label` traduit les statuts. La liste des projets utilise `with('tasks')` et affiche un aperçu des trois dernières tâches.
- **US7** : pièce jointe facultative, remplacement et téléchargement ; formats PDF, TXT, JPG/JPEG et PNG, limite de 5 Mo.
- **US8** : tests Pest 4, exécutés sur PHPUnit 12, conformément aux conventions du dépôt.

## Tests et formatage

```sh
php artisan test --compact
php artisan test --compact --group=us8
php vendor/laravel/pint/builds/pint --dirty --format agent
npm run build
```

Si l’enveloppe Artisan se bloque dans un terminal Windows, lancer Pest directement :

```sh
php vendor/pestphp/pest/bin/pest --compact
php vendor/pestphp/pest/bin/pest --compact --group=us8
```

Les tests utilisent SQLite en mémoire et `RefreshDatabase` ; les tests de fichiers utilisent `Storage::fake('public')`. Ils ne modifient pas la base de développement ni ses pièces jointes.

| Critère US8 | Couverture |
| --- | --- |
| Un visiteur est redirigé vers la connexion depuis les projets | `tests/Feature/ProjectTest.php`, test `redirects guests from project routes` |
| Un utilisateur crée un projet persisté en base | `tests/Feature/ProjectTest.php`, test `creates a project for the authenticated user despite a forged owner` |
| Un titre de tâche vide échoue à la validation | `tests/Feature/TaskTest.php`, dataset `empty title`, à la création et à la modification |

Le groupe `us8` inclut ces critères. La suite complète couvre aussi l’isolation des propriétaires, les routes imbriquées, les erreurs affichées, les dates limites, les requêtes N+1 et les pièces jointes.

## Choix techniques

Les contrôleurs Resource, relations Eloquent et Form Requests suivent les conventions Laravel. Les identifiants de propriétaire sont déterminés côté serveur via les relations, jamais à partir du formulaire. Les modèles limitent les champs assignables. Les vues réutilisent les composants Breeze, avec CSRF et affichage échappé.

La présentation reste en Blade avec Tailwind 4 déjà présent dans le projet et les dépendances fournies par Breeze. Aucun framework Vue, React ou Livewire n’est ajouté. La principale difficulté d’intégration a été l’adaptation du scaffold Breeze, qui génère encore une configuration Tailwind 3, au pipeline Tailwind 4/Vite existant.

PHP 8.4 est le niveau minimal de la plateforme Laravel 13. Le code utilise notamment les types explicites, constantes typées, attributs et l’opérateur nullsafe ; il ne revendique pas l’utilisation de fonctionnalités exclusivement introduites en PHP 8.4.

## Pièces jointes et limites connues

Les fichiers sont stockés avec un nom généré dans `storage/app/public/attachments`, conformément au sujet. `storage:link` expose ce disque : une personne disposant de l’URL publique exacte peut donc accéder au fichier. Le lien de téléchargement de l’application vérifie la propriété, mais cela ne rend pas le disque public privé. Des documents confidentiels nécessiteraient un disque privé.

Un remplacement valide supprime l’ancien fichier après sauvegarde. La suppression individuelle d’une tâche supprime sa pièce jointe. La suppression en cascade d’un projet ou d’un compte supprime les lignes SQL mais ne nettoie pas encore leurs fichiers ; un nettoyage dédié reste à prévoir.

La configuration PHP doit autoriser la limite applicative : `upload_max_filesize` au moins 5M et `post_max_size` supérieur, par exemple 8M. Ces limites serveur s’appliquent avant la validation Laravel.

Des alertes de sécurité ont été signalées par les audits Composer/npm lors de l’installation. Les mises à jour générales des dépendances n’ont pas été incluses dans les étapes fonctionnelles. Avant déploiement, examiner `composer audit` et `npm audit` et traiter leurs résultats. Aucune validation visuelle manuelle complète ni validation sous PHP 8.4 n’a encore été effectuée.

## Workflow Git

Les consignes initiales sont décrites dans les [issues du dépôt](../../issues). Le workflow du dépôt prévoit une branche `feature/<nom-stagiaire>` issue de `develop`, puis une pull request vers `develop`.

Les user stories sont enregistrées dans des commits séparés. Le travail local ne publie pas automatiquement de branche ni de pull request.
