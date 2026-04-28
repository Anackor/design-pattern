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
- `make test-unit`: run the unit suite only
- `make test-integration`: run the integration suite only
- `make phpstan`: run the base static analysis profile
- `make cs`: run the formatter in dry-run mode
- `make cs-fix`: apply formatting fixes
- `make deptrac`: run architectural diagnostics
- `make coverage`: generate coverage output in `var/coverage`
- `make quality`: run the base quality gate bundle
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

## Quality gates

The initial quality tooling is intentionally pragmatic:

- PHPStan runs at an achievable starting level
- PHP CS Fixer is available as a formatter gate and fixer
- Deptrac is configured as an architectural diagnostic, not as a required bundle gate yet
- Coverage is generated under `var/coverage`

## Observability

The project now includes a small didactic structured logger that writes JSON lines to `var/log/observability.log`.

It is intentionally applied only where observability teaches something real:

- `CreateUserProfileHandler`: logs start, user-not-found, invalid payload and success.
- `SendNotificationHandler`: logs outbound notification attempts, resolved channel and failures.
- `ActivityLogger`: turns the Observer example into a structured logging sidecar.

The rules are part of the lesson:

- event names stay stable and machine-friendly, for example `user_profile.create.started`;
- useful metadata goes into context instead of string concatenation;
- sensitive payloads are minimized, so we log identifiers, lengths or masked receivers rather than full content.

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
tests/              PHPUnit test suite split into Unit/ and Integration/
docs/planning/      Refactor roadmap and review notes
```

## Design patterns currently represented

This project is intentionally hybrid: some patterns support the HTTP/API flow, and others are didactic examples designed to be small, executable and easy to study.

| Pattern | Main example | Intent | Status |
| --- | --- | --- | --- |
| Repository | `src/Domain/Repository/*`, `src/Infrastructure/Persistence/*` | Domain-facing persistence ports with use-case language | Application flow |
| Builder | `UserProfileBuilder` | Step-by-step construction with explicit validation | Application flow |
| Factory Method | `NotificationFactory` | Create notification implementations from a typed channel | Application flow |
| Abstract Factory | `FormFactoryResolver` and form factories | Render equivalent form components for multiple channels | Didactic executable |
| Prototype | `ProductCloner`, `ProductCloneOverrides` | Clone catalog products with typed overrides | Application flow |
| Immutable Object | `DocumentVersion` | Preserve document history through new versions | Application flow |
| Singleton | `EmailTemplateRegistry` | Shared template registry with controlled reset for tests | Didactic executable |
| Factory Function | `OAuthConfigFactory` | Build OAuth config objects from provider keys | Didactic executable |
| Adapter | `FileStorageInterface` implementations | Normalize local, FTP and S3 storage operations | Application flow |
| Bridge | `ReportInterface` plus report generation wiring | Decouple report abstraction from access checking | Didactic executable |
| Composite | `Cart`, `SingleProduct`, `ProductBundle` | Treat single products and bundles uniformly | Didactic executable |
| Decorator | Application logger decorators | Layer message transformations around a logger | Didactic executable |
| Facade | `UserRegistrationFacade` | Coordinate user creation and welcome notification | Application flow |
| Flyweight | `Country`, `UserType` factories | Reuse normalized repeated values during import | Application flow |
| Proxy | `ReportProxy`, `LazyReportProxy` | Add access control or lazy creation around reports | Didactic executable |
| Chain of Responsibility | Payment validators | Stop payment validation on the first failing rule | Didactic executable |
| Command | Customer command objects plus Symfony invoker | Encapsulate customer actions as executable requests | Didactic executable |
| Iterator | `CsvFileIterator`, `CsvProcessor` | Traverse, map and filter CSV rows safely | Didactic executable |
| Mediator | `ChatRoom` | Centralize message routing between chat users | Didactic executable |
| Memento | `FormWizardSnapshot`, `FormHistoryManager` | Save and restore wizard state | Didactic executable |
| Observer | User activity subject and observers | Notify metrics/logging observers about user actions | Didactic executable |
| State | `Order` and order states | Model allowed order transitions explicitly | Didactic executable |
| Strategy | Sort strategies | Swap sorting algorithms behind a stable service | Didactic executable |
| Template Method | Request approval workflow | Reuse approval skeleton with specialized steps | Didactic executable |
| Visitor | `DiscountCart` and discount visitors | Apply external discount operations to cart items | Didactic executable |

Each listed pattern has executable code and targeted tests. The labels above are deliberately honest: "Application flow" means the pattern participates in an HTTP/use-case path, while "Didactic executable" means it is primarily a training example that remains runnable and tested.
