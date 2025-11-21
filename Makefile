.PHONY: help install init-db start migrate migrate-js import

IMPORT_FILE ?= SaveEvent/samples/event1.json

help: 
	@echo "  Commandes disponibles:"
	@echo "  make install    - Installer toutes les dépendances"
	@echo "  make init-db    - Initialiser la base MySQL"
	@echo "  make start      - Démarrer l'application web"
	@echo "  make import     - Importer un événement (IMPORT_FILE=...)"
	@echo "  make migrate    - Migration CLI (Node.js)"
	@echo "  make help       - Afficher cette aide"

install:
	@echo "Installation du projet Event Management"
	@cd SaveEvent && composer install
	@echo "Installation WebApp"
	@cd WebApp && composer install
	@echo "Installation MigrationJS"
	@cd MigrationJS && npm install
	@echo " Installation terminée"

init-db:
	@echo "Initialisation de la BDD MySQL"
	@cd WebApp && php bin/init-db.php

start:
	@echo "Démarrage sur http://localhost:8000"
	@cd WebApp && php -S localhost:8000 -t public

import:
	@echo "Import de l'événement: $(IMPORT_FILE)"
	@cd SaveEvent && ./bin/import-event.php "../$(IMPORT_FILE)"

migrate:
	@echo "Migration MongoDB vers MySQL (CLI)"
	@cd MigrationJS && node src/index.js