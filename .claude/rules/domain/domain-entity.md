---
paths: src/*/Domain/Entities/**/*.php
---

# Domain Entities & Aggregate Roots

This rule applies to Entity files in the Domain layer.

## Purpose

Entities represent domain objects with identity. Aggregate Roots are entities that serve as the entry point to an aggregate and manage domain events.

## Entity Example

```php
<?php

declare(strict_types=1);

namespace App\{Context}\Domain\Entities;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\ValueObjects\Email;
use App\Shared\Domain\ValueObjects\Timestamps;
use App\{Context}\Domain\ValueObjects\{Entity}Name;
use Ramsey\Uuid\UuidInterface;

final class {Entity} extends AggregateRoot
{
    private function __construct(
        private UuidInterface $id,
        private {Entity}Name $name,
        private Email $email,
        private Timestamps $timestamps,
    ) {
    }

    /**
     * Factory method for creating NEW entities.
     */
    public static function create(
        string $id,
        string $name,
        string $email,
        \DateTimeImmutable $createdAt,
    ): self {
        return new self(
            Uuid::fromString($id),
            new {Entity}Name($name),
            Email::fromString($email),
            Timestamps::create($createdAt),
        );
    }

    /**
     * Factory method for RECONSTRUCTING entities from persistence.
     */
    public static function make(
        string $id,
        string $name,
        string $email,
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $updatedAt = null,
        ?\DateTimeImmutable $deletedAt = null,
    ): self {
        return new self(
            Uuid::fromString($id),
            new {Entity}Name($name),
            Email::fromString($email),
            new Timestamps($createdAt, $updatedAt, $deletedAt),
        );
    }

    // Getters returning Value Objects
    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function name(): {Entity}Name
    {
        return $this->name;
    }

    // Getters returning primitive values (suffix "Value")
    public function nameValue(): string
    {
        return $this->name->value();
    }

    public function emailValue(): string
    {
        return $this->email->value();
    }

    // Timestamps delegation
    public function createdAt(): \DateTimeImmutable
    {
        return $this->timestamps->createdAt();
    }

    public function updatedAt(): ?\DateTimeImmutable
    {
        return $this->timestamps->updatedAt();
    }

    // Domain logic methods
    public function update(
        string $name,
        \DateTimeImmutable $updatedAt,
    ): void {
        $this->name = new {Entity}Name($name);
        $this->timestamps = $this->timestamps->update($updatedAt);
    }
}
```

## Rules

- **Location**: `Domain/Entities/`
- **Naming**: `{EntityName}.php` (e.g., `User.php`, `WorkEntry.php`)
- **Base Class**: Extend `App\Shared\Domain\Aggregate\AggregateRoot`
- **Class Modifier**: Use `final class` (no inheritance)
- **Constructor**: Always `private` - use named constructors

## Named Constructors

### `create()` - For New Entities

Use when creating a new entity for the first time:
```php
public static function create(
    string $id,
    string $name,
    \DateTimeImmutable $createdAt,
): self {
    return new self(
        Uuid::fromString($id),
        new EntityName($name),
        Timestamps::create($createdAt),
    );
}
```

### `make()` - For Reconstruction

Use when rebuilding from persistence (Doctrine hydration):
```php
public static function make(
    string $id,
    string $name,
    \DateTimeImmutable $createdAt,
    ?\DateTimeImmutable $updatedAt = null,
    ?\DateTimeImmutable $deletedAt = null,
): self {
    return new self(
        Uuid::fromString($id),
        new EntityName($name),
        new Timestamps($createdAt, $updatedAt, $deletedAt),
    );
}
```

## Getter Patterns

### Value Object Getters

Return the Value Object directly:
```php
public function name(): UserName
{
    return $this->name;
}

public function email(): Email
{
    return $this->email;
}
```

### Primitive Value Getters

Return primitive values with "Value" suffix:
```php
public function nameValue(): string
{
    return $this->name->value();
}

public function emailValue(): string
{
    return $this->email->value();
}
```

### ID Getter

Always return `UuidInterface`:
```php
public function id(): UuidInterface
{
    return $this->id;
}
```

## Domain Logic Methods

### Update Method

```php
public function update(
    string $name,
    \DateTimeImmutable $updatedAt,
): void {
    $this->name = new UserName($name);
    $this->timestamps = $this->timestamps->update($updatedAt);
}
```

### Immutable Transformation (when needed)

```php
public function withPasswordHashed(string $hashedPassword): self
{
    $clone = clone $this;
    $clone->password = new UserPassword($hashedPassword);
    return $clone;
}
```

### Domain Event Recording

```php
public static function create(...): self
{
    $entity = new self(...);
    $entity->record(new UserCreatedEvent($entity->id));
    return $entity;
}
```

## Timestamps Pattern

Use the shared `Timestamps` Value Object:
```php
private Timestamps $timestamps;

public function createdAt(): \DateTimeImmutable
{
    return $this->timestamps->createdAt();
}

public function updatedAt(): ?\DateTimeImmutable
{
    return $this->timestamps->updatedAt();
}

public function isDeleted(): bool
{
    return $this->timestamps->isDeleted();
}
```

## PHP Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- Use `final class` - entities should not be extended
- Private constructor - enforce named constructor usage
- Use `UuidInterface` for IDs (not string)
- Use Value Objects for domain concepts
- Constructor property promotion with trailing commas
