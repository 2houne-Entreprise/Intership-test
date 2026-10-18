# TaskFlow

TaskFlow est une application Laravel de gestion collaborative de projets et de tâches. Un projet appartient à un Owner et peut inclure des Editors et Viewers après acceptation d'une invitation dans l'application.

## Prérequis

- PHP 8.4 et Composer
- Node.js et npm
- SQLite

## Installation sous Windows PowerShell

Depuis la racine du projet:

```powershell
composer install
if (!(Test-Path .env)) { Copy-Item .env.example .env }
if (!(Test-Path database/database.sqlite)) { New-Item -ItemType File database/database.sqlite }
php artisan key:generate
php artisan migrate
npm.cmd install
npm.cmd run build
php artisan storage:link
php artisan serve
```

Ouvrez l'URL affichée par `php artisan serve`. Pour le rechargement des assets pendant le développement, lancez `npm.cmd run dev` dans un second terminal. Sur les installations où PowerShell bloque `npm.ps1`, utilisez `npm.cmd`.

## Fonctionnalités

- Inscription, connexion et déconnexion avec Laravel Breeze et Blade.
- Réinitialisation du mot de passe par code OTP envoyé par e-mail, avec code haché, expiration et limitation des tentatives. Configurez `MAIL_*` dans `.env` pour la livraison réelle des messages.
- Création, consultation, modification et suppression des projets.
- Gestion des tâches imbriquées dans un projet, avec les statuts `pending`, `in_progress` et `done`.
- Date limite facultative et filtre Eloquent `overdue()` pour les tâches en retard non terminées.
- Invitations dans l'application à un compte existant, avec choix du rôle, acceptation/refus, annulation et notification persistante.
- Rôles Owner, Editor et Viewer appliqués par Policies sur les routes et opérations serveur.
- Dashboard avec indicateurs calculés depuis SQLite, filtre de période, recherche et cartes de tâches récentes.
- Pièces jointes PDF, JPG et PNG de 2 Mo maximum, conservées sur le disque privé et téléchargées par une route autorisée.

## Choix techniques

- `User hasMany Project` conserve les projets possédés; `User belongsToMany Project` via `project_members` représente les projets partagés. `Project belongsTo User`, `Project hasMany Task` et `Task belongsTo Project` représentent propriétaire, tâches et relations métier.
- `project_members` garantit l'unicité d'une adhésion par projet/utilisateur. `project_invitations` conserve le rôle proposé et les états `pending`, `accepted`, `declined` ou `cancelled`.
- Laravel Database Notifications enregistre l'identifiant du projet, l'invitant et le rôle proposé dans SQLite; aucune invitation n'est envoyée par e-mail.
- Les migrations créent des clés étrangères avec suppression en cascade. `attachment_path` est ajouté par une migration séparée afin de ne pas modifier le schéma initial des tâches.
- `ProjectPolicy`, `TaskPolicy` et `ProjectInvitationPolicy` distinguent Owner/Editor/Viewer. `EnsureUserOwnsProject` ne laisse entrer que le propriétaire ou un membre réel; les Policies gardent chaque opération restreinte.
- `StoreTaskRequest`, `UpdateTaskRequest`, `StoreProjectInvitationRequest` et `UpdateProjectMemberRequest` valident les entrées avant leur utilisation. Les contrôleurs limitent les requêtes de tâches au projet ciblé.
- Les nouveaux fichiers sont stockés sur le disque `local` privé, servis via une route protégée par `TaskPolicy`; les anciens chemins publics restent lisibles via la même route pour compatibilité.
- Le Layout Blade partagé comprend un rail de navigation, une recherche et un compteur de notifications non lues. Les vues n'ajoutent aucun framework Frontend.
- Le Dashboard utilise `withCount`, `with` et des agrégats SQLite, sans données de démonstration codées en dur.
- La page des projets charge le nombre de tâches via `withCount`; la page de détail charge les tâches via `with` pour éviter les requêtes N+1.

## Base de données et tests

La configuration par défaut utilise SQLite (`DB_CONNECTION=sqlite`). Les migrations sont appliquées avec:

```powershell
php artisan migrate
```

Lancez les tests Feature et Unit avec:

```powershell
php artisan test --compact
```

Les tests couvrent l'accès invité, la création des projets, la validation des tâches, les permissions Owner/Editor/Viewer, les invitations et notifications, l'isolation des projets, les pièces jointes et le Dashboard.

## État de départ

Le dépôt contenait déjà les fichiers locaux de Breeze et sa dépendance; ils ont été conservés et réutilisés. Aucune migration Project/Task n'existait au départ. La configuration SQLite existait, mais le lien `public/storage` manquait; il a été créé avec `php artisan storage:link`. Aucun paquet Frontend supplémentaire n'a été ajouté.
