# Event Management

Système de gestion d'événements avec import MongoDB et interface web MySQL.

## Prérequis

- **PHP 8.3** minimum
- **Node.js 18+** (pour la migration CLI JS)
- **MongoDB**
- **MySQL**
- **Composer**
- **npm**
- **Make** (optionnel mais recommandé)

## Installation rapide


### Avec Makefile (recommandé)
Avant tout, faire des `.env.local` si besoin

```bash
# Installation complète des deux modules
make install

# Initialisation de la base MySQL
# Le mot de passe de votre utilisateur admin renseigné dans le .env est demandé (root par defaut) pour initialiser la base de donnée et créer l'utilisateur Mysql de l'app
make init-db

# Démarrage de l'application web
make start
```

### Installation manuelle

1. **SaveEvent** (MongoDB) :
```bash
cd SaveEvent
composer install
cp .env .env.local  # Ajuster si nécessaire
```

2. **WebApp** (MySQL) :

L'utilisateur admin par défaut est root pour la création de la basse de donnée et de l'utilisateur aux droits restraint utilisé par l'app.
```bash
cd WebApp
composer install
cp .env .env.local  # Ajuster si nécessaire
php bin/init-db.php
```

3. **MigrationJS** (CLI Node.js) :
```bash
cd MigrationJS
npm install
cp .env .env.local  # Ajuster si nécessaire
```

## Utilisation

### Import d'événements (SaveEvent)

Import de fichiers JSON vers MongoDB :

```bash
# Avec Makefile
make import IMPORT_FILE=mon_fichier.json

# Manuellement
cd SaveEvent
./bin/import-event.php samples/event1.json
```
Commandes Make pour les 3 fichiers de tests differents :
```bash
make import IMPORT_FILE=SaveEvent/samples/DISISFINE_format.json
make import IMPORT_FILE=SaveEvent/samples/LIVETICKET_format.json
make import IMPORT_FILE=SaveEvent/samples/TRUEGISTER_format.json
```

### Interface Web (WebApp)

L'application web permet de gérer les événements et inscriptions de la BDD MySQL via une interface graphique, ainsi qu'un bouton pour importer les donnée de MongoDB vers MySQL.

```bash
# Démarrage du serveur web
make start
# ou manuellement : 
cd WebApp && php -S localhost:8000 -t public
```

**Fonctionnalités disponibles :**
- Migration depuis MongoDB
- Liste des événements
- Création d'événements
- Modification des dates d'événements
- Suppression d'événements
- Gestion des inscriptions (ajout/suppression)

**URL :** http://localhost:8000

### Migration MongoDB → MySQL

**Option 1 : Interface Web (PHP)**
1. Importer des événements dans MongoDB (SaveEvent)
2. Démarrer l'application web (`make start`)
3. Accéder à http://localhost:8000/?action=migrate

**Option 2 : CLI Node.js**
```bash
# Avec Makefile
make migrate

# Manuellement
cd MigrationJS && node src/index.js
```

## Architecture

```
SQL_event_project/
├── SaveEvent/         # Module MongoDB (import JSON)
│   ├── bin/           # Scripts CLI PHP
│   ├── src/           # Code métier (Repository, validation MongoDB)
│   └── samples/       # Exemples JSON (3 formats)
├── WebApp/            # Application web MySQL
│   ├── public/        # Point d'entrée web
│   ├── src/           
│   │   ├── Domain/    # Entités et interfaces
│   │   └── Infrastructure/ # Controllers, repos,...
│   └── bin/           # Scripts CLI PHP
├── MigrationJS/       # Migration CLI Node.js
│   └── src/
│       ├── database/  # Connexions MongoDB/MySQL
│       ├── migrator/  # Logique de migration
│       └── normalizers/ # Transformation des formats 
├── Makefile           # Commandes unifiées
└── event_management.sql # Schéma MySQL
```

## Commandes Make

```bash
make help       # Aide et liste des commandes
make install    # Installation complète des 2 modules
make init-db    # Initialisation MySQL
make start      # Démarrage serveur web
make import IMPORT_FILE=fichier.json # Import d'événement dans MongoDB
make migrate    # Migration des evenements de MongoDB vers MySQL avec le script JS
```