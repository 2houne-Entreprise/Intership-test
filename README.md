# Application de Gestion de Projets et Tâches

## Description

Cette application a été développée avec Laravel 12 dans le cadre d'un test technique.

Elle permet à un utilisateur authentifié de :

* Créer, modifier et supprimer des projets.
* Gérer les tâches associées à chaque projet.
* Modifier rapidement le statut d'une tâche.
* Attacher des fichiers aux tâches.
* Télécharger les pièces jointes.
* Sécuriser l'accès aux projets et tâches grâce à un middleware personnalisé.
* Exécuter des tests automatisés pour garantir le bon fonctionnement de l'application.

---

## Technologies utilisées

* PHP 8.4
* Laravel 12
* Laravel Breeze
* SQLite
* Blade
* Tailwind CSS
* PHPUnit / Pest

---

## Installation

### Cloner le projet

```bash
git clone <repository-url>
cd Intership-test
```

### Installer les dépendances

```bash
composer install
npm install
```

### Configuration de l'environnement

Copier le fichier d'environnement :

```bash
copy .env.example .env
```

Générer la clé de l'application :

```bash
php artisan key:generate
```

### Configuration SQLite

Créer le fichier :

```bash
database/database.sqlite
```

Puis configurer :

```env
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite
```

### Exécuter les migrations

```bash
php artisan migrate
```

### Créer le lien de stockage

```bash
php artisan storage:link
```

### Compiler les assets

```bash
npm run build
```

ou

```bash
npm run dev
```

### Lancer le serveur

```bash
php artisan serve
```

L'application sera accessible sur :

```text
http://127.0.0.1:8000
```

---

## Exécution des tests

Lancer tous les tests :

```bash
php artisan test
```

---

## Choix techniques

### Middleware personnalisé

Un middleware `EnsureUserOwnsProject` a été créé afin de vérifier qu'un utilisateur ne peut accéder qu'à ses propres projets et tâches. Cette approche centralise la logique de sécurité et évite de répéter les vérifications dans plusieurs contrôleurs.

### Utilisation d'Eloquent

Des relations Eloquent ont été utilisées entre les modèles User, Project et Task afin de simplifier les requêtes et rendre le code plus lisible.

### Scope et Accessor

Un scope local `overdue()` permet de récupérer facilement les tâches en retard.

Un accessor `status_label` permet d'afficher un statut utilisateur plus lisible en français.

### Upload de fichiers

Les pièces jointes sont stockées via la façade Storage sur le disque public dans le dossier `attachments`.

### Tests automatisés

Des tests Feature ont été ajoutés afin de vérifier :

* l'accès sécurisé aux projets ;
* la création des projets ;
* la validation des tâches ;
* l'upload des pièces jointes.

---

## Difficultés rencontrées

La principale difficulté a été la mise en place de la sécurité entre les projets et les tâches afin de garantir qu'un utilisateur ne puisse jamais accéder aux données d'un autre utilisateur.

L'utilisation d'un middleware dédié a permis de résoudre ce problème proprement.

---

## Apport de PHP 8.4

PHP 8.4 apporte plusieurs améliorations de performance et de lisibilité du code.

Dans ce projet, l'utilisation des fonctionnalités modernes de PHP facilite la maintenance du code, améliore le typage et s'intègre parfaitement avec Laravel 12.
