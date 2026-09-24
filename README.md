# Publix

Mini-CMS éducatif pour la publication d'articles avec catégories, développé avec Symfony 7.4 et PHP 8.2+. Projet structuré en progression pédagogique sur 3 jours (lecture de données, CRUD, sécurité).

## Prérequis

- PHP 8.2 ou supérieur
- Composer
- MariaDB ou MySQL
- Symfony CLI (optionnel mais recommandé)

## Installation

1. **Cloner le dépôt**

    ```bash
    git clone <url-du-depot>
    cd symfony_press
    ```

2. **Installer les dépendances**

    ```bash
    composer install
    ```

3. **Configurer l'environnement**

    Créer un fichier `.env.local` à la racine du projet :

    ```env
    DATABASE_URL="mysql://root:password@127.0.0.1:3306/symfony_press?serverVersion=mariadb-10.11.2&charset=utf8mb4"
    ```

    Adapter les identifiants (`root`, `password`) et le port selon votre configuration MariaDB/MySQL.

4. **Créer la base de données**

    ```bash
    php bin/console doctrine:database:create
    php bin/console doctrine:migrations:migrate
    ```

5. **Importer les données de test**

    ```bash
    mysql -u root -p symfony_press < docs/database/fixtures-jour1.sql
    ```

    Entrer le mot de passe MySQL/MariaDB quand demandé.

## Démarrage

Lancer le serveur de développement Symfony :

```bash
symfony server:start
```

Accéder à l'application : **http://localhost:8000**

### Routes disponibles

- **Page d'accueil** : `/`
- **Article** : `/article/{slug}`
- **Catégorie** : `/category/{slug}`
- **Administration** : `/admin/article/*` (new, edit, delete)

## Commandes utiles

```bash
# Lister toutes les routes
php bin/console debug:router

# Vider le cache
php bin/console cache:clear

# Créer une migration après modification d'entité
php bin/console make:migration

# Installer des modules JS (après ajout dans importmap)
php bin/console importmap:install
```

## Architecture

Le projet utilise des patterns Symfony spécifiques :

- **Routes basées sur les slugs** avec `EntityValueResolver` : `{slug:article}` résout automatiquement l'entité Article par son slug
- **Auto-génération des slugs** : les articles génèrent leur slug depuis le titre via `AsciiSlugger`
- **Templates sémantiques** : HTML5 sémantique avec attributs ARIA, composants préfixés par underscore (`_article_card.html.twig`)
- **AssetMapper** : gestion des assets frontend sans étape de build
