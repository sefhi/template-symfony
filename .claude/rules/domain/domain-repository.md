---
paths: src/*/Domain/Repositories/**/*.php
---

# Domain Repository Interfaces

This rule applies to Repository interface files in the Domain layer.

## Purpose

Repository interfaces define the contract for data persistence. They are ports in the hexagonal architecture, implemented by adapters in the Infrastructure layer.

## CQRS Pattern: Separated Repositories

The project uses two repository interfaces per entity following CQRS:

1. **FindRepository** - Read operations (queries)
2. **SaveRepository** - Write operations (commands)

## FindRepository Example

```php
<?php

declare(strict_types=1);

namespace App\{Context}\Domain\Repositories;

use App\{Context}\Domain\Entities\{Entity};
use Ramsey\Uuid\UuidInterface;

interface {Entity}FindRepository
{
    public function findById(UuidInterface $id): ?{Entity};

    public function findByEmail(string $email): ?{Entity};
}
```

## SaveRepository Example

```php
<?php

declare(strict_types=1);

namespace App\{Context}\Domain\Repositories;

use App\{Context}\Domain\Entities\{Entity};

interface {Entity}SaveRepository
{
    public function save({Entity} $entity): void;

    public function delete({Entity} $entity): void;
}
```

## Rules

- **Location**: `Domain/Repositories/`
- **FindRepository Naming**: `{Entity}FindRepository.php` (e.g., `UserFindRepository.php`)
- **SaveRepository Naming**: `{Entity}SaveRepository.php` (e.g., `UserSaveRepository.php`)
- **Type**: Always `interface` (implemented in Infrastructure)

## FindRepository Patterns

### Find by ID

Always use `UuidInterface` for ID parameters:
```php
public function findById(UuidInterface $id): ?{Entity};
```

### Find by Unique Field

```php
public function findByEmail(string $email): ?{Entity};
public function findByUsername(string $username): ?{Entity};
```

### Search with Criteria

For complex queries, use Criteria pattern:
```php
use App\Shared\Domain\Criteria\Criteria;
use App\{Context}\Domain\Collections\{Entities};

public function searchAllByCriteria(Criteria $criteria): {Entities};
```

### Find All

Return typed collection:
```php
use App\{Context}\Domain\Collections\{Entities};

public function findAll(): {Entities};
```

### Count

```php
public function countByCriteria(Criteria $criteria): int;
```

## SaveRepository Patterns

### Save (Create or Update)

Single method handles both create and update:
```php
public function save({Entity} $entity): void;
```

### Delete

```php
public function delete({Entity} $entity): void;
```

## Return Types

| Operation | Return Type |
|-----------|-------------|
| `findById()` | `?{Entity}` (nullable) |
| `findBy{Field}()` | `?{Entity}` (nullable) |
| `searchAll*()` | `{Entities}` (Collection) |
| `findAll()` | `{Entities}` (Collection) |
| `count*()` | `int` |
| `save()` | `void` |
| `delete()` | `void` |

## Complete Example

```php
<?php

declare(strict_types=1);

namespace App\Template\WorkEntry\Domain\Repositories;

use App\Shared\Domain\Criteria\Criteria;
use App\Template\WorkEntry\Domain\Collections\WorkEntries;
use App\Template\WorkEntry\Domain\Entities\WorkEntry;
use Ramsey\Uuid\UuidInterface;

interface WorkEntryFindRepository
{
    public function findById(UuidInterface $id): ?WorkEntry;

    public function searchAllByCriteria(Criteria $criteria): WorkEntries;
}
```

```php
<?php

declare(strict_types=1);

namespace App\Template\WorkEntry\Domain\Repositories;

use App\Template\WorkEntry\Domain\Entities\WorkEntry;

interface WorkEntrySaveRepository
{
    public function save(WorkEntry $workEntry): void;
}
```

## Naming Conventions

| Pattern | Example |
|---------|---------|
| `{Entity}FindRepository` | `UserFindRepository` |
| `{Entity}SaveRepository` | `UserSaveRepository` |
| `findById(UuidInterface $id)` | `findById($id): ?User` |
| `findBy{Field}(type $field)` | `findByEmail(string $email): ?User` |
| `searchAllByCriteria(Criteria)` | `searchAllByCriteria($criteria): WorkEntries` |

## PHP Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- Use `UuidInterface` for ID parameters
- Use nullable return types for find operations (`?Entity`)
- Use typed collections for multi-result queries
- No implementation details in interface - pure contracts
