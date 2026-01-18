---
paths: src/**/*.php
---

# PHP Coding Style Rules

## Mandatory Rules

### Named Arguments (REQUIRED)

**Always use named arguments** when calling constructors or methods with 2+ parameters:

```php
// GOOD - Named arguments
$command = new CreateUserCommand(
    id: $id,
    name: $name,
    email: $email,
    plainPassword: $password,
    createdAt: new \DateTimeImmutable(),
);

// BAD - Positional arguments
$command = new CreateUserCommand(
    $id,
    $name,
    $email,
    $password,
    new \DateTimeImmutable(),
);
```

**Exception**: Single parameter calls don't require named arguments:

```php
// OK - Single parameter
$user = $this->repository->findById($id);
throw UserNotFoundException::withId($id);
```

### Strict Types

Every PHP file must start with:

```php
<?php

declare(strict_types=1);
```

### Null Checks

Use explicit null comparison:

```php
// GOOD
if (null === $entity) {
    throw UserNotFoundException::withId($id);
}

// BAD
if (!$entity) {
    throw UserNotFoundException::withId($id);
}
```

### Constructor Property Promotion

```php
// GOOD
public function __construct(
    private readonly UserSaveRepository $repository,
    private readonly PasswordHasher $passwordHasher,
) {
}

// BAD
private UserSaveRepository $repository;

public function __construct(UserSaveRepository $repository)
{
    $this->repository = $repository;
}
```

### Trailing Commas

Always use trailing commas in multi-line arrays, parameters, and arguments:

```php
$array = [
    'first',
    'second',
    'third',  // <-- trailing comma
];

public function __construct(
    private readonly UserSaveRepository $repository,
    private readonly PasswordHasher $passwordHasher,  // <-- trailing comma
) {
}
```

### Readonly Properties

Use `readonly` for immutable properties:

```php
public function __construct(
    private readonly UuidInterface $id,
    private readonly UserName $name,
) {
}
```

### Final and Readonly Classes

```php
// Commands, Queries, Handlers, Responses, Value Objects
final readonly class CreateUserCommand implements Command
{
    // ...
}

// Entities (not readonly - have mutable state)
final class User extends AggregateRoot
{
    // ...
}

// Controllers
final class CreateUserController extends BaseController
{
    // ...
}
```

### Invocable Pattern

Handlers and Domain Services use `__invoke()`:

```php
final readonly class CreateUserHandler implements CommandHandler
{
    public function __invoke(CreateUserCommand $command): void
    {
        // ...
    }
}
```

### Sensitive Parameters

Use `#[\SensitiveParameter]` for passwords and secrets:

```php
public function __construct(
    public string $id,
    public string $email,
    #[\SensitiveParameter] public string $plainPassword,
) {
}
```

## Class Modifiers

| Type | Modifier |
|------|----------|
| Command | `final readonly class` |
| Query | `final readonly class` |
| Command Handler | `final readonly class` |
| Query Handler | `final readonly class` |
| Response DTO | `final readonly class` |
| Value Object | `final readonly class` |
| Entity | `final class` |
| Controller | `final class` |
| Domain Service | `class` (invocable) |
| Exception | `final class` |
| Repository (Doctrine) | `final readonly class` |

## Naming Conventions

| Type | Convention | Example |
|------|------------|---------|
| Class | PascalCase | `CreateUserHandler` |
| Method | camelCase | `findById` |
| Property | camelCase | `$userRepository` |
| Constant | UPPER_SNAKE | `HTTP_CREATED` |

### Getter Conventions

```php
// Value Object getter - return VO
public function name(): UserName
{
    return $this->name;
}

// Primitive value getter - suffix "Value"
public function nameValue(): string
{
    return $this->name->value();
}

// NO "get" prefix
// BAD: getName(), getEmail()
// GOOD: name(), email()
```

## Code Organization

### Class Structure Order

1. Constants (by visibility: public, protected, private)
2. Properties (by visibility)
3. Constructor
4. Named constructors / Factory methods
5. Public methods
6. Protected methods
7. Private methods

### Import Order

```php
use App\Template\User\Domain\Entities\User;     // App namespace first
use App\Shared\Domain\Bus\Command\Command;       // Shared namespace
use Ramsey\Uuid\Uuid;                            // External packages
use Symfony\Component\HttpFoundation\Response;   // Framework
```

## PHPDoc

### @see for Command/Query linking

```php
/**
 * @see CreateUserHandler
 */
final readonly class CreateUserCommand implements Command
{
    // ...
}
```

### Type hints preferred over PHPDoc

```php
// GOOD - Type hint
public function findById(UuidInterface $id): ?User

// AVOID - PHPDoc for types that can be hinted
/** @param UuidInterface $id */
public function findById($id)
```

### PHPDoc for arrays

```php
/**
 * @param array<string, mixed> $overrides
 */
public static function random(array $overrides = []): User

/**
 * @return array<class-string<\Throwable>, int>
 */
protected function exceptions(): array
```
