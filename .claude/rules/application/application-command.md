---
paths: src/*Context/Application/Command/**/*.php
---

# Application Command Handler Rules & Conventions

This rule applies to all Command Handler files in the Application layer.

## Purpose

Command Handlers execute write operations by coordinating domain objects. They orchestrate the domain layer to fulfill use cases that modify state.

## Code Example

```php
<?php

declare(strict_types=1);

namespace App\{Context}\Application\Command\{Entity};

use App\{Context}\Domain\Command\{Entity}\Create{Entity};
use App\{Context}\Domain\Exception\{Entity}\{Entity}NotFound;
use App\{Context}\Domain\Model\{Entity}\{Entity};
use App\{Context}\Domain\Model\{Entity}\{Entity}Repository;

class Create{Entity}Handler
{
    public function __construct(
        private readonly {Entity}Repository $repository,
    ) {
    }

    public function handle(Create{Entity}Command $command): void
    {
        // 1. Validate preconditions (entity exists, etc.)
        // 2. Create/update domain entity
        // 3. Persist via repository

        $entity = {Entity}::create(
           id: $command->id,
           userId: $command->userId
        );
        $this->repository->save($entity);
    }
}
```

## Rules

- **Location**: `Application/Command/{Entity}/`
- **Naming**: `{Action}{Entity}Handler.php` (e.g., `CreateComplaintHandler.php`)
- **Method**: `handle({DomainCommand} $command): void`
- **Dependencies**: Inject repositories and domain service interfaces
- **Return type**: Usually `void`, exceptions for special cases
- **Validation**: Throw domain exceptions for error cases (e.g., `{Entity}NotFound`)

## Handler Patterns

### Create Handler

```php
public function handle(Create{Entity} $command): void
{
    $dependency = $this->dependencyRepository->byId($command->dependencyId());
    if (null === $dependency) {
        throw new DependencyNotFound();
    }

    $entity = new {Entity}($command, $dependency);
    $this->repository->save($entity);
}
```

### Update Handler

```php
public function handle(Update{Entity} $command): void
{
    $entity = $this->repository->byId($command->entityId());

    if (null === $entity) {
        throw new {Entity}NotFound();
    }

    $entity->update($command);
    $this->repository->save($entity);
}
```

### Delete Handler

```php
public function handle(Delete{Entity} $command): void
{
    $entity = $this->repository->byId($command->entityId());

    if (null === $entity) {
        throw new {Entity}NotFound();
    }

    $this->repository->delete($entity);
}
```

### Side Effect Handler (notifications, emails)

```php
public function handle(SendEmail{Entity} $command): void
{
    $entity = $this->repository->byId($command->entityId());

    if (null === $entity) {
        return; // Silent return for side effects
    }

    $this->mailClient->send(...);
}
```

## Cross-Context Communication

When handlers need data from other contexts, use Domain Service interfaces (Ports).

```php
public function __construct(
    private readonly {Entity}Repository $repository,
    private readonly CoreClient $coreClient, // Domain Service interface
) {
}

public function handle(Create{Entity} $command): void
{
    $company = $this->coreClient->findCompany($command->companyId());

    if (null === $company) {
        throw new CompanyNotFound();
    }

    $entity = new {Entity}($command, $company);
    $this->repository->save($entity);
}
```

## Naming Conventions

| Pattern | Example |
|---------|---------|
| `Create{Entity}Handler` | `CreateComplaintHandler` |
| `Update{Entity}Handler` | `UpdateComplaintHandler` |
| `Delete{Entity}Handler` | `DeleteComplaintHandler` |
| `{Action}{Entity}Handler` | `ApprovePayrollHandler`, `SendEmailComplaintHandler` |

## PHP Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- Explicit null checks: `null === $entity` instead of `!$entity`
- Constructor property promotion with trailing commas
- Use `readonly` for immutable properties
- Add `@see` docblock linking to test class
