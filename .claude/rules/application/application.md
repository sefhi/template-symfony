---
paths: src/*Context/Application/**/*.php
---

# Application Layer Rules & Conventions

This rule applies to all PHP files in the Application layer of bounded contexts.

## Fundamental Principles

1. **Use Case Orchestration**: The Application layer orchestrates domain operations through handlers
2. **CQRS Pattern**: Commands (write) and Queries (read) are strictly separated
3. **Thin Handlers**: Business logic belongs in Domain; handlers only coordinate
4. **Domain Dependencies Only**: Application layer depends ONLY on Domain layer

## Forbidden Dependencies

The Application layer MUST NOT import from:
- `Symfony\*` - No framework dependencies (except base classes in rare cases)
- `Doctrine\*` - No ORM dependencies
- `Infrastructure\*` - No infrastructure layer dependencies

Allowed dependencies:
- `App\{Context}\Domain\*` - Domain layer of same context
- `App\Shared\Domain\*` - Base domain classes

## Application Layer Structure

```
Application/
├── Commands/{Action}{Entity}/           # Commands & Command Handlers (write operations)
│   ├── {Action}{Entity}Command.php
│   └── {Action}{Entity}Handler.php
├── Queries/{Entity}/             # Queries & Query Handlers (read operations)
│   ├── Find{Entity}sQuery.php
│   └── Find{Entity}sQueryHandler.php
```

## Command Handlers

Command Handlers execute write operations by coordinating domain objects. They orchestrate the domain layer to fulfill use cases that modify state.

- **Location**: `Application/Command/{Entity}/`
- **Naming**: `{Action}{Entity}Handler.php` (e.g., `CreateComplaintHandler.php`)
- **Method**: `__invoke({Command} $command): void`

For detailed conventions, code examples, and handler patterns, see `application-command.md`.

## Queries

Queries are read-only request objects that define what data is needed. They implement permission interfaces and contain filter/pagination parameters via QueryParams.

- **Location**: `Application/Query/{Entity}/`
- **Naming**: `Find{Entity}sQuery.php` (always plural, filters determine results)

For detailed conventions, code examples, and query patterns, see `application-query.md`.

## Query Handlers

Query Handlers execute read operations by querying ViewRepositories and returning data. They are responsible for optimized read access to domain data.

- **Location**: `Application/Query/{Entity}/`
- **Naming**: `{QueryName}Handler.php` (e.g., `FindComplaintsQueryHandler.php`)
- **Return type**: `QueryResponse` from `Sesame\Ddd\Domain\QueryResponse`

For detailed conventions, code examples, and handler patterns, see `application-query.md`.

## Cross-Context Communication

When handlers need data from other contexts, use Domain Service interfaces (Ports).

```php
// In handler
public function __construct(
    private readonly {Entity}Repository $repository,
    private readonly UserClient $userClient, 
) {
}

public function __invoke(Create{Entity}Command $command): void
{
    $user = $this->userClient->findById($command->userId);

    if (null === $user) {
        throw new UserNotFound::withId($command->userId);
    }

    $entity = {Entity}::create(
       id: $command->id,
       userId: $command->userId
    );
    $this->repository->save($entity);
}
```

## Naming Conventions Summary

| Type | Pattern                          | Example                  |
|------|----------------------------------|--------------------------|
| Command Handler | `{Action}{Entity}Handler`        | `CreateComplaintHandler` |
| Command | `{Action}{Entity}Command`        | `CreateComplaintCommand` |
| Query | `Find{Entity}sQuery`             | `FindComplaintsQuery`    |
| Query Handler | `Find{Entity}sHandler`           | `FindComplaintsHandler`  |
| Handler Method | `__invoke` | `__invoke`               |

## PHP Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- Explicit null checks: `null === $entity` instead of `!$entity`
- Constructor property promotion with trailing commas (for new code)
- Use `readonly` for immutable properties in DTOs
