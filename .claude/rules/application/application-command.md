---
paths: src/*/Application/Commands/**/*.php
---

# Application Command & Handler Rules & Conventions

This rule applies to all Command and Command Handler files in the Application layer.

## Purpose

Commands are write-only request objects that define what should happen.
Command Handlers execute write operations by coordinating domain objects. They orchestrate the domain layer to fulfill use cases that modify state.

## Command Example

```php
<?php

declare(strict_types=1);

namespace App\{Context}\Application\Commands\Create{Entity};

use App\Shared\Domain\Bus\Command\Command;

/**
 * @see Create{Entity}Handler
 */
final readonly class Create{Entity}Command implements Command
{
    public function __construct(
        public string $id,
        public string $name,
        public \DateTimeImmutable $createdAt,
    ) {
    }
}
```

## Command Handler Example

```php
<?php

declare(strict_types=1);

namespace App\{Context}\Application\Commands\Create{Entity};

use App\{Context}\Domain\Entities\{Entity};
use App\{Context}\Domain\Repositories\{Entity}SaveRepository;
use App\Shared\Domain\Bus\Command\CommandHandler;

final readonly class Create{Entity}Handler implements CommandHandler
{
    public function __construct(
        private {Entity}SaveRepository $repository,
    ) {
    }

    public function __invoke(Create{Entity}Command $command): void
    {
        // 1. Validate preconditions (entity exists, etc.)
        // 2. Create/update domain entity
        // 3. Persist via repository

        $entity = {Entity}::create(
           $command->id,
           $command->name,
           $command->createdAt,
        );
        $this->repository->save($entity);
    }
}
```

## Rules

- **Location**: `Application/Commands/{Action}{Entity}/`
- **Command Naming**: `{Action}{Entity}Command.php` (e.g., `CreateUserCommand.php`)
- **Handler Naming**: `{Action}{Entity}Handler.php` (e.g., `CreateUserHandler.php`)
- **Command Interface**: `App\Shared\Domain\Bus\Command\Command`
- **Handler Interface**: `App\Shared\Domain\Bus\Command\CommandHandler`
- **Method**: `__invoke({Command} $command): void`
- **Dependencies**: Inject repositories and domain service interfaces
- **Return type**: Usually `void`, exceptions for special cases
- **Validation**: Throw domain exceptions for error cases (e.g., `{Entity}NotFoundException`)
- **Class modifier**: Use `final readonly` for Commands and Handlers

## Handler Patterns

### Create Handler

```php
public function __invoke(Create{Entity}Command $command): void
{
    $entity = {Entity}::create(
        $command->id,
        $command->name,
        $command->createdAt,
    );
    $this->repository->save($entity);
}
```

### Update Handler

```php
public function __invoke(Update{Entity}Command $command): void
{
    $entity = ($this->ensureExists{Entity}ByIdService)(Uuid::fromString($command->id));

    $entity->update(
        $command->name,
        new \DateTimeImmutable(),
    );
    $this->repository->save($entity);
}
```

### Delete Handler

```php
public function __invoke(Delete{Entity}Command $command): void
{
    $entity = ($this->ensureExists{Entity}ByIdService)(Uuid::fromString($command->id));

    $this->repository->delete($entity);
}
```

## Cross-Context Communication

When handlers need data from other contexts, use Domain Service interfaces (Ports).

```php
public function __construct(
    private readonly {Entity}SaveRepository $repository,
    private readonly UserFindRepository $userRepository,
) {
}

public function __invoke(Create{Entity}Command $command): void
{
    $user = $this->userRepository->findById(Uuid::fromString($command->userId));

    if (null === $user) {
        throw UserNotFoundException::withId($command->userId);
    }

    $entity = {Entity}::create(
        $command->id,
        $command->userId,
    );
    $this->repository->save($entity);
}
```

## Naming Conventions

| Pattern | Example |
|---------|---------|
| `Create{Entity}Command` | `CreateUserCommand` |
| `Create{Entity}Handler` | `CreateUserHandler` |
| `Update{Entity}Command` | `UpdateUserCommand` |
| `Update{Entity}Handler` | `UpdateUserHandler` |
| `Delete{Entity}Command` | `DeleteUserCommand` |
| `Delete{Entity}Handler` | `DeleteUserHandler` |
| `{Action}{Entity}Command` | `ClockInCommand`, `ClockOutCommand` |
| `{Action}{Entity}Handler` | `ClockInHandler`, `ClockOutHandler` |

## PHP Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- Explicit null checks: `null === $entity` instead of `!$entity`
- Constructor property promotion with trailing commas
- Use `readonly` for immutable properties
- Use `final readonly class` for Commands and Handlers
- Add `@see` docblock linking Command to Handler
