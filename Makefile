SHELL := /bin/bash

.PHONY: init install

# PROJECT INIT
install: build up c-install migrate status
	@echo ""
	@echo "Install is finished successfully!"

build:
	@echo "Building docker services..."
	DOCKER_BUILDKIT=1 COMPOSE_DOCKER_CLI_BUILD=1 docker compose build
	@echo ""

status:
	@echo ""
	@echo "Current local status is:"
	git status -sb
	docker compose ps

up:
	docker compose up -d

down:
	docker compose down --remove-orphans

bash:
	docker compose exec app /bin/bash

c-install:
	@echo "Executing composer install inside app container..."
	docker compose exec -T app bash -c 'XDEBUG_MODE=off composer install'

migrate:
	@echo "Running artisan migrations & seeders inside app container..."
	docker compose exec -T app bash -c 'XDEBUG_MODE=off php bin/console d:m:m --no-interaction'

traefik-install: traefik-network-create traefik

traefik-network-create:
	docker network create --driver=bridge --attachable --internal=false traefik-public

traefik:
	docker compose -f traefik.docker-compose.yml up -d
