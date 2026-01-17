---
paths: src/*/Domain/Exceptions/**/*.php
---

# Domain Exceptions

This rule applies to Exception files in the Domain layer.

## Purpose

Domain Exceptions represent business rule violations and domain-specific error conditions. They communicate what went wrong in domain terms.

## Exception Example

```php
<?php

declare(strict_types=1);

namespace App\{Context}\Domain\Exceptions;

use Ramsey\Uuid\UuidInterface;

final class {Entity}NotFoundException extends \DomainException
{
    public static function withId(UuidInterface $id): self
    {
        return new self(sprintf('{Entity} with id %s not found', $id->toString()));
    }
}
```

## Rules

- **Location**: `Domain/Exceptions/`
- **Naming**: `{Entity}{Situation}Exception.php` (e.g., `UserNotFoundException.php`)
- **Base Class**: Extend `\DomainException`
- **Class Modifier**: Use `final class`
- **Constructor**: Private - use factory methods

## Factory Method Patterns

### Not Found Exception

```php
final class UserNotFoundException extends \DomainException
{
    public static function withId(UuidInterface $id): self
    {
        return new self(sprintf('User with id %s not found', $id->toString()));
    }
}
```

### Already Exists Exception

```php
final class UserAlreadyExistsException extends \DomainException
{
    public static function withEmail(string $email): self
    {
        return new self(sprintf('User with email %s already exists', $email));
    }
}
```

### Invalid State Exception

```php
final class WorkEntryAlreadyClockedInException extends \DomainException
{
    public static function withId(UuidInterface $id): self
    {
        return new self(sprintf('Work entry with id %s already clocked in', $id->toString()));
    }
}
```

### Condition Not Met Exception

```php
final class WorkEntryNotClockedInException extends \DomainException
{
    public static function withId(UuidInterface $id): self
    {
        return new self(sprintf('Work entry with id %s is not clocked in', $id->toString()));
    }
}
```

### Ownership/Permission Exception

```php
final class WorkEntryNotBelongToUserException extends \DomainException
{
    public static function withId(UuidInterface $workEntryId): self
    {
        return new self(
            sprintf('Work entry with id %s does not belong to user', $workEntryId->toString())
        );
    }
}
```

## Multiple Factory Methods

```php
final class UserNotFoundException extends \DomainException
{
    public static function withId(UuidInterface $id): self
    {
        return new self(sprintf('User with id %s not found', $id->toString()));
    }

    public static function withEmail(string $email): self
    {
        return new self(sprintf('User with email %s not found', $email));
    }
}
```

## Naming Conventions

| Situation | Pattern | Example |
|-----------|---------|---------|
| Entity not found | `{Entity}NotFoundException` | `UserNotFoundException` |
| Entity already exists | `{Entity}AlreadyExistsException` | `UserAlreadyExistsException` |
| Action already done | `{Entity}Already{Action}Exception` | `WorkEntryAlreadyClockedInException` |
| Condition not met | `{Entity}Not{Condition}Exception` | `WorkEntryNotClockedInException` |
| Ownership violation | `{Entity}NotBelongTo{Owner}Exception` | `WorkEntryNotBelongToUserException` |
| Invalid value | `Invalid{Concept}Exception` | `InvalidEmailException` |

## HTTP Status Code Mapping

Exceptions are mapped to HTTP codes in Controllers:
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

## Usage in Handlers/Services

```php
// In Domain Service
public function __invoke(UuidInterface $id): User
{
    $user = $this->userFindRepository->findById($id);

    if (null === $user) {
        throw UserNotFoundException::withId($id);
    }

    return $user;
}
```

```php
// In Entity
public function clockOut(\DateTimeImmutable $endDate): void
{
    if (null !== $this->endDate) {
        throw WorkEntryAlreadyClockedOutException::withId($this->id);
    }

    $this->endDate = $endDate;
}
```

## PHP Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- Use `final class` - exceptions should not be extended
- Extend `\DomainException` for domain-level exceptions
- Use descriptive factory method names (`withId`, `withEmail`, etc.)
- Use `sprintf()` for message formatting
- Include relevant context in error messages
