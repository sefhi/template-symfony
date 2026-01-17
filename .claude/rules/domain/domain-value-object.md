---
paths: src/*/Domain/ValueObjects/**/*.php
---

# Domain Value Objects

This rule applies to Value Object files in the Domain layer.

## Purpose

Value Objects are immutable objects that represent domain concepts with no identity. Two Value Objects with the same values are considered equal.

## Simple Value Object Example

For simple string-based Value Objects, extend `StringValueObject`:

```php
<?php

declare(strict_types=1);

namespace App\{Context}\Domain\ValueObjects;

use App\Shared\Domain\ValueObjects\StringValueObject;

final readonly class {Attribute}Name extends StringValueObject
{
}
```

That's it! The base class provides:
- Constructor with validation
- `value(): string` method
- `__toString()` method
- `isEqualTo()` method

## Value Object with Validation Example

```php
<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObjects;

final readonly class Email extends StringValueObject
{
    public function __construct(string $value)
    {
        $this->ensureIsValidEmail($value);
        parent::__construct($value);
    }

    public static function fromString(string $email): self
    {
        return new self($email);
    }

    private function ensureIsValidEmail(string $email): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException(
                sprintf('<%s> is not a valid email address', $email)
            );
        }
    }
}
```

## Complex Value Object Example

For Value Objects with multiple fields:

```php
<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObjects;

final readonly class Timestamps
{
    public function __construct(
        private \DateTimeImmutable $createdAt,
        private ?\DateTimeImmutable $updatedAt = null,
        private ?\DateTimeImmutable $deletedAt = null,
    ) {
    }

    public static function create(\DateTimeImmutable $createdAt): self
    {
        return new self($createdAt);
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function deletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function update(\DateTimeImmutable $updatedAt): self
    {
        return new self($this->createdAt, $updatedAt, $this->deletedAt);
    }

    public function delete(\DateTimeImmutable $deletedAt): self
    {
        return new self($this->createdAt, $this->updatedAt, $deletedAt);
    }

    public function isDeleted(): bool
    {
        return null !== $this->deletedAt;
    }
}
```

## Rules

- **Location**: `Domain/ValueObjects/`
- **Naming**: `{AttributeName}.php` (e.g., `UserName.php`, `UserPassword.php`)
- **Class Modifier**: Always `final readonly class`
- **Immutability**: Value Objects are immutable - never modify, create new instances

## Base Classes (from Shared)

### StringValueObject

For string-based Value Objects:
```php
abstract readonly class StringValueObject
{
    public function __construct(protected string $value) {}

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }
}
```

### IntValueObject

For integer-based Value Objects:
```php
abstract readonly class IntValueObject
{
    public function __construct(protected int $value) {}

    public function value(): int
    {
        return $this->value;
    }
}
```

## Patterns

### Factory Method

For Value Objects with validation:
```php
public static function fromString(string $value): self
{
    return new self($value);
}
```

### Immutable Transformation

Return new instance instead of modifying:
```php
public function update(\DateTimeImmutable $updatedAt): self
{
    return new self($this->createdAt, $updatedAt, $this->deletedAt);
}
```

### Equality Check

```php
public function isEqualTo(self $other): bool
{
    return $this->value === $other->value;
}
```

### Boolean Check Methods

```php
public function isDeleted(): bool
{
    return null !== $this->deletedAt;
}

public function isEmpty(): bool
{
    return '' === $this->value;
}
```

## Naming Conventions

| Pattern | Example |
|---------|---------|
| `{Entity}{Attribute}` | `UserName`, `UserPassword` |
| `{Concept}` | `Email`, `Timestamps` |
| Simple string VO | Extend `StringValueObject` |
| Complex VO | Use `final readonly class` |

## PHP Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- Use `final readonly class` - Value Objects must be immutable
- Use constructor property promotion
- Validation in constructor (fail fast)
- Return new instances for transformations
- No setters - immutability is mandatory
