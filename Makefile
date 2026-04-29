COMPOSE_FILE := docker/docker-compose.yml
COMPOSE := docker compose -f $(COMPOSE_FILE)
APP_SERVICE := app
DB_SERVICE := mysql
APP_WORKDIR := /var/www/symfony
COVERAGE_DIR := var/coverage
CONSOLE_ARGS ?=
COMPOSER_ARGS ?=
PHPUNIT_ARGS ?=

.PHONY: build start stop logs ps shell console composer composer-validate install lint-container test test-unit test-integration test-functional observability-demo phpstan cs cs-fix deptrac deptrac-check coverage quality pr-checks db migrate

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

composer-validate:
	$(COMPOSE) run --rm $(APP_SERVICE) sh -lc "git config --global --add safe.directory $(APP_WORKDIR) >/dev/null 2>&1 || true; composer validate"

install:
	$(COMPOSE) run --rm $(APP_SERVICE) sh -lc "git config --global --add safe.directory $(APP_WORKDIR) >/dev/null 2>&1 || true; composer install"

lint-container:
	$(COMPOSE) run --rm $(APP_SERVICE) php bin/console lint:container

test:
	$(COMPOSE) run --rm $(APP_SERVICE) php vendor/bin/phpunit $(PHPUNIT_ARGS)

test-unit:
	$(COMPOSE) run --rm $(APP_SERVICE) php vendor/bin/phpunit --testsuite Unit $(PHPUNIT_ARGS)

test-integration:
	$(COMPOSE) run --rm $(APP_SERVICE) php vendor/bin/phpunit --testsuite Integration $(PHPUNIT_ARGS)

test-functional:
	$(COMPOSE) run --rm $(APP_SERVICE) php vendor/bin/phpunit --testsuite Functional $(PHPUNIT_ARGS)

observability-demo:
	$(COMPOSE) run --rm $(APP_SERVICE) php bin/console app:observability:demo --reset-log

phpstan:
	$(COMPOSE) run --rm $(APP_SERVICE) sh -lc "git config --global --add safe.directory $(APP_WORKDIR) >/dev/null 2>&1 || true; composer qa:phpstan"

cs:
	$(COMPOSE) run --rm $(APP_SERVICE) sh -lc "git config --global --add safe.directory $(APP_WORKDIR) >/dev/null 2>&1 || true; composer qa:cs"

cs-fix:
	$(COMPOSE) run --rm $(APP_SERVICE) sh -lc "git config --global --add safe.directory $(APP_WORKDIR) >/dev/null 2>&1 || true; composer qa:cs-fix"

deptrac:
	$(COMPOSE) run --rm $(APP_SERVICE) sh -lc "git config --global --add safe.directory $(APP_WORKDIR) >/dev/null 2>&1 || true; composer qa:deptrac || true"

deptrac-check:
	$(COMPOSE) run --rm $(APP_SERVICE) sh -lc "git config --global --add safe.directory $(APP_WORKDIR) >/dev/null 2>&1 || true; composer qa:deptrac"

coverage:
	$(COMPOSE) run --rm $(APP_SERVICE) sh -lc "git config --global --add safe.directory $(APP_WORKDIR) >/dev/null 2>&1 || true; mkdir -p $(COVERAGE_DIR) && composer qa:coverage"

quality: phpstan cs test

pr-checks: composer-validate lint-container phpstan cs deptrac-check test-unit test-integration test-functional

db:
	$(COMPOSE) exec $(DB_SERVICE) mysql -u api_user -papi_pass api_db

migrate:
	$(COMPOSE) run --rm $(APP_SERVICE) php bin/console doctrine:migrations:migrate --no-interaction
