---
paths: src/*/Application/Queries/**/*.php
---

# Application Layer - Queries & Query Handlers

This rule applies to Query and QueryHandler files in the Application layer.

## Purpose

Queries are read-only request objects that define what data is needed.
Query Handlers execute read operations and return response DTOs.

## Query Example

```php
<?php

declare(strict_types=1);

namespace App\{Context}\Application\Queries\Find{Entity}ById;

use App\Shared\Domain\Bus\Query\Query;

/**
 * @see Find{Entity}ByIdHandler
 */
final readonly class Find{Entity}ByIdQuery implements Query
{
    public function __construct(
        public string $id,
    ) {
    }
}
```

## Query Handler Example

```php
<?php

declare(strict_types=1);

namespace App\{Context}\Application\Queries\Find{Entity}ById;

use App\{Context}\Domain\Services\EnsureExists{Entity}ByIdService;
use App\Shared\Domain\Bus\Query\QueryHandler;
use Ramsey\Uuid\Uuid;

final readonly class Find{Entity}ByIdHandler implements QueryHandler
{
    public function __construct(
        private EnsureExists{Entity}ByIdService $ensureExists{Entity}ByIdService,
    ) {
    }

    public function __invoke(Find{Entity}ByIdQuery $query): {Entity}Response
    {
        $id = Uuid::fromString($query->id);

        return {Entity}Response::from{Entity}(($this->ensureExists{Entity}ByIdService)($id));
    }
}
```

## Query Response Example

```php
<?php

declare(strict_types=1);

namespace App\{Context}\Application\Queries\Find{Entity}ById;

use App\{Context}\Domain\Entities\{Entity};
use App\Shared\Domain\Bus\Query\QueryResponse;

final readonly class {Entity}Response implements \JsonSerializable, QueryResponse
{
    public function __construct(
        public string $id,
        public string $name,
        public \DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $updatedAt,
    ) {
    }

    public static function from{Entity}({Entity} $entity): self
    {
        return new self(
            $entity->id()->toString(),
            $entity->name(),
            $entity->createdAt(),
            $entity->updatedAt(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id'        => $this->id,
            'name'      => $this->name,
            'createdAt' => $this->createdAt->format(\DateTimeInterface::ATOM),
            'updatedAt' => $this->updatedAt?->format(\DateTimeInterface::ATOM),
        ];
    }
}
```

## Query Rules

- **Location**: `Application/Queries/{Action}{Entity}/`
- **Naming**: `{Action}{Entity}Query.php` (e.g., `FindUserByIdQuery.php`, `ListWorkEntryQuery.php`)
- **Interface**: `App\Shared\Domain\Bus\Query\Query`
- **Properties**: Use `public readonly` with constructor promotion
- **Class modifier**: Use `final readonly`

## Query Handler Rules

- **Location**: `Application/Queries/{Action}{Entity}/`
- **Naming**: `{Action}{Entity}Handler.php` (e.g., `FindUserByIdHandler.php`)
- **Interface**: `App\Shared\Domain\Bus\Query\QueryHandler`
- **Method**: `__invoke({Query} $query): {Entity}Response`
- **Return type**: Custom response DTO implementing `QueryResponse`
- **Dependencies**: Use FindRepository or Domain Services
- **Class modifier**: Use `final readonly`

## Query Response Rules

- **Location**: Same folder as Query and Handler
- **Naming**: `{Entity}Response.php` (e.g., `UserResponse.php`)
- **Interface**: `App\Shared\Domain\Bus\Query\QueryResponse` and `\JsonSerializable`
- **Factory method**: `from{Entity}({Entity} $entity): self`
- **Class modifier**: Use `final readonly`

## Query Handler Patterns

### Find By Id Query

```php
public function __invoke(Find{Entity}ByIdQuery $query): {Entity}Response
{
    $id = Uuid::fromString($query->id);
    $entity = ($this->ensureExists{Entity}ByIdService)($id);

    return {Entity}Response::from{Entity}($entity);
}
```

### List Query

```php
public function __invoke(List{Entity}Query $query): List{Entity}Response
{
    $entities = $this->repository->findAll();

    return List{Entity}Response::from{Entities}($entities);
}
```

## Naming Conventions

| Pattern | Use Case | Example |
|---------|----------|---------|
| `Find{Entity}ByIdQuery` | Find single entity by ID | `FindUserByIdQuery` |
| `Find{Entity}ByIdHandler` | Handle find by ID | `FindUserByIdHandler` |
| `List{Entity}Query` | List/collection queries | `ListWorkEntryQuery` |
| `List{Entity}Handler` | Handle list query | `ListWorkEntryHandler` |
| `{Entity}Response` | Single entity response | `UserResponse` |
| `List{Entity}Response` | Collection response | `ListWorkEntryResponse` |
| `Get{Entity}Query` | Get current context entity | `GetUserMeQuery` |

## PHP Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- Constructor property promotion with trailing commas
- Use `readonly` for immutable properties
- Use `final readonly class` for Queries, Handlers, and Responses
- Add `@see` docblock linking Query to Handler
