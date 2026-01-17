---
paths: src/*/Domain/**/*.php
---

# Domain Layer Rules & Conventions

This rule applies to all PHP files in the Domain layer of bounded contexts.

## Fundamental Principles

1. **Business Logic Home**: All business rules and domain logic live here
2. **Framework Agnostic**: No Symfony, Doctrine, or infrastructure dependencies
3. **Pure PHP**: Only depends on Shared domain classes and PHP standard library
4. **Immutability**: Prefer immutable objects (Value Objects, readonly classes)

## Forbidden Dependencies

The Domain layer MUST NOT import from:
- `Symfony\*` - No framework dependencies
- `Doctrine\*` - No ORM dependencies
- `Infrastructure\*` - No infrastructure layer dependencies
- `Application\*` - Domain doesn't know about Application layer

Allowed dependencies:
- `App\{Context}\Domain\*` - Same context domain classes
- `App\Shared\Domain\*` - Shared domain classes (AggregateRoot, ValueObjects, Bus interfaces)
- `Ramsey\Uuid\*` - UUID library (considered domain primitive)

## Domain Layer Structure

```
Domain/
├── Entities/                    # Aggregate roots and entities
│   └── {Entity}.php
├── Repositories/                # Repository interfaces (ports)
│   ├── {Entity}FindRepository.php
│   └── {Entity}SaveRepository.php
├── ValueObjects/                # Immutable value objects
│   └── {Attribute}.php
├── Exceptions/                  # Domain exceptions
│   └── {Entity}{Situation}Exception.php
├── Services/                    # Domain services
│   └── {Action}{Entity}Service.php
├── Collections/                 # Typed collections
│   └── {Entities}.php
└── Security/                    # Security interfaces (ports)
    └── {SecurityConcern}.php
```

## Key Patterns

### Aggregate Root

Entities extend `AggregateRoot` for domain event support:
```php
final class User extends AggregateRoot
{
    // Private constructor - use named constructors
    private function __construct(...) {}

    // Factory for new entities
    public static function create(...): self {}

    // Factory for reconstruction from persistence
    public static function make(...): self {}
}
```

### Repository Separation (CQRS)

Two interfaces per entity:
- `{Entity}FindRepository` - Read operations
- `{Entity}SaveRepository` - Write operations

### Value Objects

Immutable objects for domain concepts:
```php
final readonly class UserName extends StringValueObject {}
```

### Domain Services

Invocable services for cross-entity operations:
```php
class EnsureExistsUserByIdService
{
    public function __invoke(UuidInterface $id): User {}
}
```

For detailed conventions, see the specific rule files:
- `domain-entity.md` - Entities and Aggregate Roots
- `domain-repository.md` - Repository interfaces
- `domain-value-object.md` - Value Objects
- `domain-exception.md` - Domain Exceptions
- `domain-service.md` - Domain Services

## Naming Conventions Summary

| Type | Pattern | Example |
|------|---------|---------|
| Entity | `{EntityName}` | `User`, `WorkEntry` |
| Find Repository | `{Entity}FindRepository` | `UserFindRepository` |
| Save Repository | `{Entity}SaveRepository` | `UserSaveRepository` |
| Value Object | `{AttributeName}` | `UserName`, `UserPassword` |
| Exception | `{Entity}{Situation}Exception` | `UserNotFoundException` |
| Service | `{Action}{Entity}Service` | `EnsureExistsUserByIdService` |
| Collection | `{Entities}` (plural) | `WorkEntries` |

## PHP Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- Explicit null checks: `null === $entity` instead of `!$entity`
- Constructor property promotion with trailing commas
- Use `readonly` for immutable properties
- Use `final` to prevent inheritance where appropriate
- Use `UuidInterface` from Ramsey for IDs
