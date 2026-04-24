COMPOSE_FILE := docker/docker-compose.yml
COMPOSE := docker compose -f $(COMPOSE_FILE)
APP_SERVICE := app
DB_SERVICE := mysql
APP_WORKDIR := /var/www/symfony
CONSOLE_ARGS ?=
COMPOSER_ARGS ?=
PHPUNIT_ARGS ?=

.PHONY: build start stop logs ps shell console composer install test db migrate

build:
	$(COMPOSE) build

start:
	$(COMPOSE) up -d

stop:
	$(COMPOSE) down

logs:
	$(COMPOSE) logs -f

ps:
	$(COMPOSE) ps

shell:
	$(COMPOSE) exec $(APP_SERVICE) bash

console:
	$(COMPOSE) run --rm $(APP_SERVICE) php bin/console $(CONSOLE_ARGS)

composer:
	$(COMPOSE) run --rm $(APP_SERVICE) sh -lc "git config --global --add safe.directory $(APP_WORKDIR) >/dev/null 2>&1 || true; composer $(COMPOSER_ARGS)"

install:
	$(COMPOSE) run --rm $(APP_SERVICE) sh -lc "git config --global --add safe.directory $(APP_WORKDIR) >/dev/null 2>&1 || true; composer install"

test:
	$(COMPOSE) run --rm $(APP_SERVICE) php vendor/bin/phpunit $(PHPUNIT_ARGS)

db:
	$(COMPOSE) exec $(DB_SERVICE) mysql -u api_user -papi_pass api_db

migrate:
	$(COMPOSE) run --rm $(APP_SERVICE) php bin/console doctrine:migrations:migrate --no-interaction
