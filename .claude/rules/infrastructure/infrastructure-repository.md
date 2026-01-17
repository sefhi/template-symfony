---
paths: src/*/Infrastructure/Persistence/**/*.php
---

# Infrastructure Doctrine Repositories

This rule applies to Doctrine Repository implementations in the Infrastructure layer.

## Purpose

Doctrine Repositories implement the domain repository interfaces (ports) using Doctrine ORM. They translate between domain objects and database persistence.

## FindRepository Example

```php
<?php

declare(strict_types=1);

namespace App\{Context}\Infrastructure\Persistence\Repositories;

use App\{Context}\Domain\Entities\{Entity};
use App\{Context}\Domain\Repositories\{Entity}FindRepository;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;
use Ramsey\Uuid\UuidInterface;

final readonly class Doctrine{Entity}FindRepository extends DoctrineRepository
    implements {Entity}FindRepository
{
    public function findById(UuidInterface $id): ?{Entity}
    {
        return $this->repository({Entity}::class)->findOneBy(['id' => $id]);
    }

    public function findByEmail(string $email): ?{Entity}
    {
        return $this->repository({Entity}::class)->findOneBy([
            'email.value' => $email,
        ]);
    }
}
```

## SaveRepository Example

```php
<?php

declare(strict_types=1);

namespace App\{Context}\Infrastructure\Persistence\Repositories;

use App\{Context}\Domain\Entities\{Entity};
use App\{Context}\Domain\Repositories\{Entity}SaveRepository;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;

final readonly class Doctrine{Entity}SaveRepository extends DoctrineRepository
    implements {Entity}SaveRepository
{
    public function save({Entity} $entity): void
    {
        $this->persist($entity);
    }

    public function delete({Entity} $entity): void
    {
        $this->remove($entity);
    }
}
```

## Repository Rules

- **Location**: `Infrastructure/Persistence/Repositories/`
- **Find Naming**: `Doctrine{Entity}FindRepository.php`
- **Save Naming**: `Doctrine{Entity}SaveRepository.php`
- **Base Class**: Extend `App\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository`
- **Interface**: Implement corresponding domain repository interface
- **Class Modifier**: Use `final readonly class`

## FindRepository Patterns

### Find by ID

```php
public function findById(UuidInterface $id): ?User
{
    return $this->repository(User::class)->findOneBy(['id' => $id]);
}
```

### Find by Value Object Field

Access nested Value Object properties with dot notation:
```php
public function findByEmail(string $email): ?User
{
    return $this->repository(User::class)->findOneBy([
        'email.value' => $email,  // email is embedded VO
    ]);
}
```

### Search with Criteria

For complex queries using the Criteria pattern:
```php
use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineCriteriaConverter;

final readonly class DoctrineWorkEntryFindRepository extends DoctrineRepository
    implements WorkEntryFindRepository
{
    /** @var array<string, string> */
    private array $criteriaToDoctrineFields;

    /** @var array<string, class-string> */
    private array $hydrators;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager);

        $this->criteriaToDoctrineFields = [
            'id'        => 'id',
            'userId'    => 'userId',
            'createdAt' => 'timestamps.createdAt',
        ];

        $this->hydrators = [
            'createdAt' => \DateTimeImmutable::class,
        ];
    }

    public function searchAllByCriteria(Criteria $criteria): WorkEntries
    {
        $doctrineCollection = $this->repository(WorkEntry::class)->matching(
            DoctrineCriteriaConverter::convert(
                $criteria,
                $this->criteriaToDoctrineFields,
                $this->hydrators
            )
        );

        return WorkEntries::fromArray($doctrineCollection->toArray());
    }
}
```

## SaveRepository Patterns

### Save (Persist)

```php
public function save(User $user): void
{
    $this->persist($user);
}
```

### Delete (Remove)

```php
public function delete(User $user): void
{
    $this->remove($user);
}
```

## Doctrine Mapping (XML)

### Entity Mapping

```xml
<!-- Infrastructure/Persistence/Doctrine/Mapping/Entities/User.orm.xml -->
<?xml version="1.0" encoding="UTF-8"?>
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping">
    <entity name="App\Template\User\Domain\Entities\User" table="app_users">
        <id name="id" type="uuid" length="36"/>

        <embedded name="name"
                  class="App\Template\User\Domain\ValueObjects\UserName"
                  use-column-prefix="false"/>

        <embedded name="email"
                  class="App\Shared\Domain\ValueObjects\Email"
                  use-column-prefix="false"/>

        <embedded name="password"
                  class="App\Template\User\Domain\ValueObjects\UserPassword"
                  use-column-prefix="false"/>

        <embedded name="timestamps"
                  class="App\Shared\Domain\ValueObjects\Timestamps"
                  use-column-prefix="false"/>
    </entity>
</doctrine-mapping>
```

### Value Object Mapping (Embeddable)

```xml
<!-- Infrastructure/Persistence/Doctrine/Mapping/ValueObjects/UserName.orm.xml -->
<?xml version="1.0" encoding="UTF-8"?>
<doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping">
    <embeddable name="App\Template\User\Domain\ValueObjects\UserName">
        <field name="value" type="string" length="100" column="name"/>
    </embeddable>
</doctrine-mapping>
```

### Entity with Relations

```xml
<entity name="App\Template\WorkEntry\Domain\Entities\WorkEntry" table="app_work_entry">
    <id name="id" type="uuid" length="36"/>

    <field name="userId" type="uuid" length="36"/>
    <field name="startDate" type="datetime_immutable" nullable="true"/>
    <field name="endDate" type="datetime_immutable" nullable="true"/>

    <embedded name="timestamps"
              class="App\Shared\Domain\ValueObjects\Timestamps"
              use-column-prefix="false"/>

    <indexes>
        <index name="idx_work_entry_user_id" columns="user_id"/>
    </indexes>
</entity>
```

## Naming Conventions

| Type | Pattern | Example |
|------|---------|---------|
| Find Repository | `Doctrine{Entity}FindRepository` | `DoctrineUserFindRepository` |
| Save Repository | `Doctrine{Entity}SaveRepository` | `DoctrineUserSaveRepository` |
| Entity Mapping | `{Entity}.orm.xml` | `User.orm.xml` |
| VO Mapping | `{ValueObject}.orm.xml` | `UserName.orm.xml` |

## Mapping Location

```
Infrastructure/Persistence/Doctrine/Mapping/
├── Entities/
│   ├── User.orm.xml
│   └── WorkEntry.orm.xml
└── ValueObjects/
    ├── UserName.orm.xml
    └── UserPassword.orm.xml
```

## PHP Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- Use `final readonly class`
- Implement domain repository interface
- Extend `DoctrineRepository` base class
- Use typed collections for multi-result queries
- Use dot notation for embedded Value Object fields
