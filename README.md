# Event Management

## Prérequis

- **PHP 8.3** minimum
- **MongoDB** installé et démarré
- **Composer** pour la gestion des dépendances

## Partie SaveEvent (mongoDB)

### Installation

1. Aller dans le dossier SaveEvent :
```bash
cd SaveEvent
```

2. Installer les dépendances :
```bash
composer install
```

3. Ajuster les variable d'environnement si nécessaire après avoir créer un .env.local :
```bash
cp .env .env.local
```

### Utilisation

#### Importer un événement JSON
Toujours depuis le dossier SaveEvent:
```bash
./bin/import-event.php <fichier.json>s
```

#### Exemples
Les exemples du cours sont à disposition dans `SaveEvent/samples` pour tester la commande.