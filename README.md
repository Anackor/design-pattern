# Design Pattern API

Symfony 7.2 / PHP 8.2+ project used as a didactic catalogue of design patterns with a real HTTP application around it. The current refactor starts by making the repository easy to boot, test and review before deeper architectural work continues.

## Official execution path

The supported local path is Docker. This matters because the current Windows setup does not have `php` or `composer` in `PATH`, while the repository already ships a Docker workflow.

- Official compose source: `docker/docker-compose.yml`
- Compatibility mirror at repo root: `compose.yaml`
- Development database: MySQL 8

## Requirements

- Docker Desktop with Compose support
- GNU Make (optional, but recommended)

Local PHP/Composer are optional. If you have them installed, you can still use them, but the documented workflow below assumes Docker only.

## Quick start

1. Build the containers:

```sh
make build
```

2. Start the stack:

```sh
make start
```

3. Install PHP dependencies inside Docker:

```sh
make install
```

4. Run database migrations:

```sh
make migrate
```

5. Run the test suite:

```sh
make test
```

Application endpoints are exposed at:

- API / Apache: `http://localhost:8000`
- phpMyAdmin: `http://localhost:8081`

## Useful commands

- `make ps`: show container status
- `make logs`: follow service logs
- `make shell`: open a shell in the PHP container
- `make console CONSOLE_ARGS="lint:container"`: run Symfony console commands
- `make composer COMPOSER_ARGS="validate"`: run Composer commands inside Docker
- `make stop`: stop and remove the stack

If you do not use `make`, the equivalent Docker command shape is:

```sh
docker compose -f docker/docker-compose.yml run --rm app php bin/console lint:container
```

## Environment

- Base committed defaults live in `.env`
- Safe template values live in `.env.example`
- Machine-specific overrides should go in `.env.local`

Environment variable names are aligned around:

- `FTP_HOST`, `FTP_USER`, `FTP_PASSWORD`
- `AWS_REGION`, `AWS_BUCKET`, `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`

## Repository structure

```text
src/
  Application/      Application services, handlers and DTOs
  Domain/           Domain model and contracts
  Infrastructure/   Persistence and external adapters
  Presentation/     HTTP controllers
config/             Symfony configuration
docker/             Docker source of truth
migrations/         Doctrine migrations
tests/              PHPUnit test suite
docs/planning/      Refactor roadmap and review notes
```

## Design patterns currently represented

- Repository
- Builder
- Factory Method
- Abstract Factory
- Prototype
- Immutable Object
- Singleton
- Factory Function
- Adapter
- Bridge
- Composite
- Decorator
- Facade
- Flyweight
- Proxy
- Chain of Responsibility
- Command
- Iterator
- Mediator
- Memento
- Observer
- State
- Strategy
- Template Method
- Visitor

The project is still being tightened so that each pattern is backed by a real use case, executable code and tests.
