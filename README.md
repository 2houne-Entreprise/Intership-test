README.md complet qui resume les taches du projet :

1. Cloner le projet et installer les dépendances :

git clone <URL_DU_DEPOT>
cd taskflow
composer install
npm install && npm run dev

2. Configurer SQLite 

3. Installer les dépendances front (Breeze)

4. Configurer la base de données

Fonctionnalités principales (Résumé)
Authentification (register, login, logout) avec Laravel Breeze.
CRUD Projets avec accès limité à l’utilisateur propriétaire.
Gestion des Tâches liées aux projets :
Statuts : pending, in_progress, done
Validation avec Form Requests
Upload de fichiers attachés
Sécurité via middleware EnsureUserOwnsProject.
Eloquent avancé : Scopes pour tâches en retard, Accessors pour statut en français, Eager loading pour performance.

Choix techniques et difficultés rencontrées
Laravel 13 et Breeze
Utilisation de Breeze Blade pour un setup simple et fonctionnel.
Permet de se concentrer sur le backend et la logique métier.
Eloquent et relations
User → Project → Task avec hasMany et belongsTo.
Eager loading pour performances : auth()->user()->projects()->with('tasks')->get().
Form Requests pour validation
Permet de centraliser la logique de validation pour Task.
Gestion automatique des erreurs avec affichage dans les vues Blade (old('title')).
Middleware
EnsureUserOwnsProject protège les routes sensibles.
Simplifie la sécurité sans répéter les vérifications dans chaque controller.
Storage et Upload de fichiers
Utilisation de Storage::disk('public')->put(...) pour gérer les attachments.
Lien symbolique php artisan storage:link pour rendre les fichiers accessibles publiquement.
Tests
Tests unitaires et fonctionnels avec PHPUnit pour sécuriser la logique :
Redirection des invités
Création de projet
Validation des tâches

Choix techniques et difficultés (Résumé)
Laravel 13 + Breeze : setup simple avec Blade, focus sur le backend.
Eloquent et relations : User → Project → Task avec hasMany/belongsTo, et eager loading pour optimiser les requêtes.
Form Requests : centralisent la validation des tâches et affichage automatique des erreurs.
Middleware : EnsureUserOwnsProject sécurise les routes sensibles.
Storage / Upload : gestion des fichiers attachés via Storage::disk('public') et php artisan storage:link.
Tests : PHPUnit pour garantir la sécurité et le bon fonctionnement (accès invité, création de projet, validation des tâches).
