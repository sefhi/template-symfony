# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Rules Reference

For detailed conventions, consult the rules in `.claude/rules/`:

| Topic | Rule Files |
|-------|------------|
| **Overview** | `architecture.md`, `coding-style.md` |
| Domain | `domain/domain.md`, `domain-entity.md`, `domain-repository.md`, `domain-value-object.md`, `domain-exception.md`, `domain-service.md` |
| Application | `application/application.md`, `application-command.md`, `application-query.md` |
| Infrastructure | `infrastructure/infrastructure.md`, `infrastructure-controller.md`, `infrastructure-repository.md` |
| Tests | `tests/tests.md`, `tests-unit.md`, `tests-functional.md`, `tests-mother.md` |

---

## Build & Development Commands

All commands run via Docker. Start containers first with `make start`.

```bash
# Installation
make install          # Full setup: deps + JWT keys + database
make deps             # Install composer dependencies

# Testing
make test             # Run all PHPUnit tests
make test/coverage    # Run tests with coverage

# Code Quality
make lint             # Fix code style (PHP-CS-Fixer)
make static-analysis  # PHPStan analysis (level: max)
make style            # Run lint + static-analysis

# Database
make migrate          # Run migrations (dev + test)
make migration/diff   # Generate migration from entity changes

# Docker
make start            # Start containers
make bash             # SSH into container
```

**Running a single test:**
```bash
docker compose exec webserver php bin/phpunit tests/Unit/Path/To/TestFile.php
docker compose exec webserver php bin/phpunit --filter testMethodName
```

---

## Architecture

This is a **DDD + CQRS** Symfony 7.2 application with PHP 8.4.

### Context Structure

Each bounded context follows this structure:
```
src/{Context}/{SubModule}/
├── Domain/           # Entities, Repository interfaces, Value Objects, Exceptions, Services
├── Application/      # Commands, Queries, Handlers, Response DTOs
└── Infrastructure/   # Controllers, Request DTOs, Doctrine repositories
```

**Current contexts:** `Health`, `BookStore`, `Template` (User, WorkEntry, TimeTracking), `Subtitle`

**Context Documentation:** See `docs/contexts/{context-name}/README.md` for business logic documentation.

### Message Buses

Uses Symfony Messenger with separate buses:
- `command.bus` / `command.sync.bus` - Write operations (Commands)
- `query.bus` - Read operations (Queries)
- `event.bus` - Domain events

Handlers are auto-registered via `_instanceof` in `config/services.yaml`.

### Key Patterns

| Pattern | Description |
|---------|-------------|
| **Entities** | `final class` extending `AggregateRoot`, private constructor with `create()`/`make()` factories |
| **Repositories** | CQRS split: `{Entity}FindRepository` (read) / `{Entity}SaveRepository` (write) |
| **Value Objects** | `final readonly class` with factory methods |
| **Commands/Queries** | `final readonly class` implementing `Command`/`Query` interface |
| **Handlers** | `final readonly class` implementing `CommandHandler`/`QueryHandler` with `__invoke()` |
| **Controllers** | `final class` extending `BaseController` with `__invoke()` |
| **Request DTOs** | Validation via `#[Assert\*]` attributes, `#[MapRequestPayload]` |
| **Mother Objects** | Test data factories: `{Entity}Mother::random()` |

### Entity Creation

Entities use named constructors:
```php
$user = User::create(id: $id, email: $email, password: $password);  // New entity
$user = User::make(id: $id, email: $email, password: $password);    // From DB
```

### Testing Structure

```
tests/
├── Unit/           # Handler tests with mocked dependencies
├── Integration/    # Doctrine repository tests
├── Functional/     # API endpoint tests
└── Utils/          # Mother objects, Factories, helpers
```

Tests use GIVEN-WHEN-THEN comments, `#[Test]` attributes, and `itShould{Action}{Condition}` naming.

---

## API Endpoints

- Health: `GET /api/health`
- Auth: `POST /api/login` (JWT)
- Users: `/api/users` (CRUD)
- Work Entries: `/api/work-entries` (CRUD + clock-in/clock-out)
- Subtitles: `/api/subtitles` (Upload, List, Get, Delete)

---

## Code Quality Standards

- `declare(strict_types=1)` in ALL files
- PHPStan level: **max**
- PHP-CS-Fixer: `@Symfony` + `@PSR12` rules
- `final readonly class` for Commands, Queries, Handlers, Responses, Value Objects
- `final class` for Entities, Controllers
- Constructor property promotion with trailing commas
- Explicit null checks: `null === $entity`
- Named arguments for 2+ parameters
