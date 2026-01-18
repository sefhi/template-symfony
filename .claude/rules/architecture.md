---
paths: src/**/*.php
---

# DDD & CQRS Architecture Rules

This document defines the architectural rules and patterns for the project following Domain-Driven Design (DDD) and CQRS principles with PHP 8.4/Symfony 7.2.

## Layer Structure

```
src/{Context}/{SubModule}/
├── Application/                         # Application Layer (Use Cases)
│   ├── Commands/{Action}{Entity}/       # Commands & Handlers (write operations)
│   │   ├── {Action}{Entity}Command.php
│   │   └── {Action}{Entity}Handler.php
│   └── Queries/{Action}{Entity}/        # Queries & Handlers (read operations)
│       ├── {Action}{Entity}Query.php
│       ├── {Action}{Entity}Handler.php
│       └── {Entity}Response.php
├── Domain/                              # Domain Layer (Business Logic)
│   ├── Entities/                        # Aggregate Roots and Entities
│   ├── Repositories/                    # Repository Interfaces (Ports)
│   │   ├── {Entity}FindRepository.php
│   │   └── {Entity}SaveRepository.php
│   ├── ValueObjects/                    # Value Objects
│   ├── Exceptions/                      # Domain Exceptions
│   ├── Services/                        # Domain Services
│   ├── Collections/                     # Typed Collections
│   └── Security/                        # Security Interfaces (Ports)
└── Infrastructure/                      # Infrastructure Layer (Adapters)
    ├── Api/                             # HTTP Layer
    │   ├── {Action}{Entity}/
    │   │   ├── {Action}{Entity}Controller.php
    │   │   └── {Action}{Entity}Request.php
    │   └── routes.yaml
    ├── Persistence/                     # Database Layer
    │   ├── Repositories/
    │   │   ├── Doctrine{Entity}FindRepository.php
    │   │   └── Doctrine{Entity}SaveRepository.php
    │   └── Doctrine/Mapping/
    │       ├── Entities/{Entity}.orm.xml
    │       └── ValueObjects/{VO}.orm.xml
    └── Security/                        # Security Implementations
```

## Dependency Rules

### Allowed Dependencies
1. **Domain Layer** → No dependencies on other layers (pure business logic)
2. **Application Layer** → Can depend on Domain layer only
3. **Infrastructure Layer** → Can depend on Application and Domain layers

### Forbidden Dependencies
- Domain MUST NOT import from `Symfony\*` or `Doctrine\*`
- Domain MUST NOT import from Infrastructure layer
- Application MUST NOT import from Infrastructure layer
- Infrastructure is the ONLY layer that can import framework classes

### Allowed in Domain
- `App\{Context}\Domain\*` - Same context domain classes
- `App\Shared\Domain\*` - Shared domain classes (AggregateRoot, ValueObjects, Bus interfaces)
- `Ramsey\Uuid\*` - UUID library (considered domain primitive)

## Layer-Specific Rules

Each layer has detailed architecture documentation:

| Layer | Rule File | Key Components |
|-------|-----------|----------------|
| Domain | `domain/domain.md` | Entities, Value Objects, Repositories, Services, Exceptions |
| Application | `application/application.md` | Commands, Queries, Handlers, Response DTOs |
| Infrastructure | `infrastructure/infrastructure.md` | Controllers, Request DTOs, Doctrine Repositories |
| Tests | `tests/tests.md` | Unit, Integration, Functional tests, Mother Objects |

## CQRS Pattern

### Commands (Write Operations)
- Located in `Application/Commands/{Action}{Entity}/`
- Implement `App\Shared\Domain\Bus\Command\Command`
- Handlers implement `CommandHandler` with `__invoke()`
- Return `void`

### Queries (Read Operations)
- Located in `Application/Queries/{Action}{Entity}/`
- Implement `App\Shared\Domain\Bus\Query\Query`
- Handlers implement `QueryHandler` with `__invoke()`
- Return Response DTOs implementing `QueryResponse`

### Repository Separation
- `{Entity}FindRepository` - Read operations
- `{Entity}SaveRepository` - Write operations

## Key Patterns

### Entity Named Constructors
```php
final class User extends AggregateRoot
{
    private function __construct(...) {}

    public static function create(...): self {}  // New entity
    public static function make(...): self {}    // Reconstruct from persistence
}
```

### Invocable Handlers and Services
```php
public function __invoke(CreateUserCommand $command): void
{
    // ...
}
```

### Domain Exceptions with Factory Methods
```php
final class UserNotFoundException extends \DomainException
{
    public static function withId(UuidInterface $id): self
    {
        return new self(sprintf('User with id %s not found', $id->toString()));
    }
}
```

## Naming Conventions

| Type | Pattern | Example |
|------|---------|---------|
| Command | `{Action}{Entity}Command` | `CreateUserCommand` |
| Command Handler | `{Action}{Entity}Handler` | `CreateUserHandler` |
| Query | `{Action}{Entity}Query` | `FindUserByIdQuery` |
| Query Handler | `{Action}{Entity}Handler` | `FindUserByIdHandler` |
| Response DTO | `{Entity}Response` | `UserResponse` |
| Find Repository | `{Entity}FindRepository` | `UserFindRepository` |
| Save Repository | `{Entity}SaveRepository` | `UserSaveRepository` |
| Doctrine Repository | `Doctrine{Entity}{Operation}Repository` | `DoctrineUserFindRepository` |
| Exception | `{Entity}{Situation}Exception` | `UserNotFoundException` |
| Controller | `{Action}{Entity}Controller` | `CreateUserController` |
| Request DTO | `{Action}{Entity}Request` | `CreateUserRequest` |
| Domain Service | `{Action}{Entity}Service` | `EnsureExistsUserByIdService` |
| Value Object | `{Concept}` | `UserName`, `UserPassword` |

## Key Contexts

| Context | Sub-modules | Purpose |
|---------|-------------|---------|
| **Template** | User, WorkEntry, TimeTracking | Core entities and time tracking |
| **BookStore** | Book | Example bounded context |
| **Health** | - | Health check endpoint |
| **Shared** | Domain, Infrastructure | Shared components |

## Error Handling

### Exception Mapping in Controllers

```php
protected function exceptions(): array
{
    return [
        UserNotFoundException::class => Response::HTTP_NOT_FOUND,           // 404
        UserAlreadyExistsException::class => Response::HTTP_CONFLICT,       // 409
        WorkEntryNotBelongToUserException::class => Response::HTTP_FORBIDDEN, // 403
    ];
}
```

## PHP Coding Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- `final class` or `final readonly class` as appropriate
- Constructor property promotion with trailing commas
- Explicit null checks: `null === $var` instead of `!$var`
- Use `readonly` for immutable properties
- Use named arguments for 2+ parameters

See `coding-style.md` for detailed coding conventions.
