---
paths: src/*/Domain/Services/**/*.php
---

# Domain Services

This rule applies to Service files in the Domain layer.

## Purpose

Domain Services encapsulate domain logic that doesn't naturally fit within a single Entity. They orchestrate operations across multiple domain objects or perform validations.

## Domain Service Example

```php
<?php

declare(strict_types=1);

namespace App\{Context}\Domain\Services;

use App\{Context}\Domain\Entities\{Entity};
use App\{Context}\Domain\Exceptions\{Entity}NotFoundException;
use App\{Context}\Domain\Repositories\{Entity}FindRepository;
use Ramsey\Uuid\UuidInterface;

class EnsureExists{Entity}ByIdService
{
    public function __construct(
        private readonly {Entity}FindRepository $repository,
    ) {
    }

    public function __invoke(UuidInterface $id): {Entity}
    {
        $entity = $this->repository->findById($id);

        if (null === $entity) {
            throw {Entity}NotFoundException::withId($id);
        }

        return $entity;
    }
}
```

## Rules

- **Location**: `Domain/Services/`
- **Naming**: `{Action}{Entity}Service.php` or `{Action}{Entity}By{Criteria}Service.php`
- **Pattern**: Invocable class with `__invoke()` method
- **Stateless**: No internal state - all data passed via constructor (dependencies) or method params
- **Class Modifier**: Use `class` (not `final` - allows testing flexibility)

## Common Service Patterns

### Ensure Exists Service

Validates entity exists or throws exception:
```php
class EnsureExistsUserByIdService
{
    public function __construct(
        private readonly UserFindRepository $userFindRepository,
    ) {
    }

    public function __invoke(UuidInterface $userId): User
    {
        $user = $this->userFindRepository->findById($userId);

        if (null === $user) {
            throw UserNotFoundException::withId($userId);
        }

        return $user;
    }
}
```

### Ensure Unique Service

Validates uniqueness before creation:
```php
class EnsureUniqueUserEmailService
{
    public function __construct(
        private readonly UserFindRepository $userFindRepository,
    ) {
    }

    public function __invoke(string $email): void
    {
        $existingUser = $this->userFindRepository->findByEmail($email);

        if (null !== $existingUser) {
            throw UserAlreadyExistsException::withEmail($email);
        }
    }
}
```

### Ownership Validation Service

Validates entity belongs to user:
```php
class EnsureWorkEntryBelongsToUserService
{
    public function __invoke(WorkEntry $workEntry, UuidInterface $userId): void
    {
        if (!$workEntry->userId()->equals($userId)) {
            throw WorkEntryNotBelongToUserException::withId($workEntry->id());
        }
    }
}
```

### Domain Calculation Service

Performs domain calculations:
```php
class CalculateWorkHoursService
{
    public function __invoke(WorkEntries $entries): float
    {
        $totalHours = 0.0;

        foreach ($entries as $entry) {
            if (null !== $entry->endDate()) {
                $diff = $entry->endDate()->diff($entry->startDate());
                $totalHours += $diff->h + ($diff->i / 60);
            }
        }

        return $totalHours;
    }
}
```

## Usage in Application Layer

Domain Services are injected into Handlers:
```php
final readonly class FindUserByIdHandler implements QueryHandler
{
    public function __construct(
        private EnsureExistsUserByIdService $ensureExistsUserByIdService,
    ) {
    }

    public function __invoke(FindUserByIdQuery $query): UserResponse
    {
        $userId = Uuid::fromString($query->id);

        // Service is invocable - called like a function
        $user = ($this->ensureExistsUserByIdService)($userId);

        return UserResponse::fromUser($user);
    }
}
```

## Naming Conventions

| Pattern | Example |
|---------|---------|
| `EnsureExists{Entity}ByIdService` | `EnsureExistsUserByIdService` |
| `EnsureUnique{Entity}{Field}Service` | `EnsureUniqueUserEmailService` |
| `Ensure{Entity}BelongsTo{Owner}Service` | `EnsureWorkEntryBelongsToUserService` |
| `Calculate{Concept}Service` | `CalculateWorkHoursService` |
| `Validate{Concept}Service` | `ValidatePasswordStrengthService` |

## Security Interfaces (Domain Ports)

Security-related interfaces live in `Domain/Security/`:

```php
// Domain/Security/PasswordHasher.php
interface PasswordHasher
{
    public function hashPlainPassword(
        User $user,
        #[\SensitiveParameter] string $plainPassword
    ): string;
}

// Domain/Security/AuthenticatedUserProvider.php
interface AuthenticatedUserProvider
{
    public function currentUser(): User;
}
```

These are implemented in Infrastructure layer.

## PHP Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- Use `__invoke()` for single-responsibility services
- Constructor property promotion with `private readonly`
- Explicit null checks: `null === $entity`
- Throw domain exceptions for validation failures
- Use `#[\SensitiveParameter]` for sensitive data (passwords, tokens)
