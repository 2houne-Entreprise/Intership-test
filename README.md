# TaskFlow - Gestionnaire de tâches collaboratif

## 📋 Description

Application web de gestion de projets et de tâches développée avec Laravel 13 dans le cadre d'un test technique pour un stage.

## 🚀 Installation

### Prérequis
- PHP 8.4+
- Composer
- Node.js (pour Breeze)

### Étapes

```bash
# 1. Cloner le projet
git clone https://github.com/votre-compte/Intership-test.git
cd Intership-test

# 2. Installer les dépendances
composer install
npm install
npm run build

# 3. Configurer l'environnement
cp .env.example .env

# 4. Créer la base de données SQLite
touch database/database.sqlite

# 5. Configurer .env pour SQLite
# DB_CONNECTION=sqlite

# 6. Exécuter les migrations
php artisan migrate

# 7. Créer le lien storage pour les uploads
php artisan storage:link

# 8. Lancer le serveur
php artisan serve
🎯 Choix techniques

Pourquoi Laravel 13 ?

Dernière version stable
Support PHP 8.4
Performance améliorée
Architecture utilisée

MVC classique : Modèles, Vues Blade, Contrôleurs
Policies : Pour la sécurité (alternative aux middlewares)
FormRequests : Validation séparée
Eager Loading : Évite le problème N+1
Blade Components : Composant x-alert réutilisable