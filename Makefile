.PHONY: help install init-db start migrate

IMPORT_FILE ?= SaveEvent/samples/event1.json

help: 
	@echo "  Commandes disponibles:"
	@echo "  make install    - Installer toutes les dépendances"
	@echo "  make init-db    - Initialiser la base MySQL"
	@echo "  make start      - Démarrer l'application web"
	@echo "  make import     - Importer un événement (IMPORT_FILE=...)"
	@echo "  make migrate    - Instructions pour la migration"
	@echo "  make help       - Afficher cette aide"

install:
	@echo "Installation du projet Event Management"
	@cd SaveEvent && composer install
	@echo "Installation WebApp"
	@cd WebApp && composer install
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

migrate: ## Instructions pour la migration
	@echo "🔄 Migration MongoDB vers MySQL:"
	@echo "1. Assurez-vous que l'app web est démarrée (make start)"
	@echo "2. Accédez à http://localhost:8000/?action=migrate"