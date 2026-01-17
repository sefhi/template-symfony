---
paths: tests/Integration/**/*.php
---

# Integration Tests

This rule applies to Integration test files.

## Purpose

Integration tests verify repository implementations against a real database. They use transactions that rollback after each test to maintain isolation.

## Repository Test Example

```php
<?php

declare(strict_types=1);

namespace Tests\Integration\{Context}\Infrastructure\Persistence\Repository;

use App\{Context}\Domain\Entities\{Entity};
use App\{Context}\Infrastructure\Persistence\Repositories\Doctrine{Entity}FindRepository;
use App\{Context}\Infrastructure\Persistence\Repositories\Doctrine{Entity}SaveRepository;
use PHPUnit\Framework\Attributes\Test;
use Tests\Integration\BaseDoctrineIntegrationTestCase;
use Tests\Unit\{Context}\Domain\Entities\{Entity}Mother;

final class Doctrine{Entity}RepositoryTest extends BaseDoctrineIntegrationTestCase
{
    private Doctrine{Entity}SaveRepository $repositorySave;
    private Doctrine{Entity}FindRepository $repositoryFind;

    protected function setUp(): void
    {
        parent::setUp();

        $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->repositorySave = new Doctrine{Entity}SaveRepository($entityManager);
        $this->repositoryFind = new Doctrine{Entity}FindRepository($entityManager);
    }

    #[Test]
    public function itShouldSave{Entity}(): void
    {
        // GIVEN
        ${entity} = {Entity}Mother::random();

        // WHEN
        $this->repositorySave->save(${entity});

        // THEN
        self::assertEquals(
            $this->repositoryFind->findById(${entity}->id()),
            ${entity}
        );
    }

    #[Test]
    public function itShouldFindById(): void
    {
        // GIVEN
        ${entity} = {Entity}Mother::random();
        $this->repositorySave->save(${entity});

        // WHEN
        $result = $this->repositoryFind->findById(${entity}->id());

        // THEN
        self::assertNotNull($result);
        self::assertEquals(${entity}->id(), $result->id());
    }

    #[Test]
    public function itShouldReturnNullWhenNotFound(): void
    {
        // GIVEN
        $nonExistentId = Uuid::fromString(MotherCreator::id());

        // WHEN
        $result = $this->repositoryFind->findById($nonExistentId);

        // THEN
        self::assertNull($result);
    }
}
```

## Rules

- **Location**: `tests/Integration/{Context}/Infrastructure/Persistence/Repository/`
- **Naming**: `Doctrine{Entity}RepositoryTest.php`
- **Base Class**: `Tests\Integration\BaseDoctrineIntegrationTestCase`
- **Class Modifier**: Use `final class`

## Base Test Case

The base class handles database transactions:

```php
abstract class BaseDoctrineIntegrationTestCase extends KernelTestCase
{
    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->entityManager->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->entityManager->rollback();
    }
}
```

## Test Structure

### Properties

```php
private DoctrineUserSaveRepository $repositorySave;
private DoctrineUserFindRepository $repositoryFind;
```

### setUp Method

Always call `parent::setUp()` first:
```php
protected function setUp(): void
{
    parent::setUp();

    $entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
    $this->repositorySave = new DoctrineUserSaveRepository($entityManager);
    $this->repositoryFind = new DoctrineUserFindRepository($entityManager);
}
```

## Common Test Patterns

### Test Save Operation

```php
#[Test]
public function itShouldSaveEntity(): void
{
    // GIVEN
    $entity = EntityMother::random();

    // WHEN
    $this->repositorySave->save($entity);

    // THEN
    $found = $this->repositoryFind->findById($entity->id());
    self::assertEquals($entity, $found);
}
```

### Test Find By Field

```php
#[Test]
public function itShouldFindByEmail(): void
{
    // GIVEN
    $user = UserMother::random();
    $this->repositorySave->save($user);

    // WHEN
    $result = $this->repositoryFind->findByEmail($user->emailValue());

    // THEN
    self::assertNotNull($result);
    self::assertEquals($user->id(), $result->id());
}
```

### Test Delete Operation

```php
#[Test]
public function itShouldDeleteEntity(): void
{
    // GIVEN
    $entity = EntityMother::random();
    $this->repositorySave->save($entity);

    // WHEN
    $this->repositorySave->delete($entity);

    // THEN
    $result = $this->repositoryFind->findById($entity->id());
    self::assertNull($result);
}
```

### Test Search with Criteria

```php
#[Test]
public function itShouldSearchByCriteria(): void
{
    // GIVEN
    $userId = Uuid::fromString(MotherCreator::id());
    $entry1 = WorkEntryMother::random(['userId' => $userId->toString()]);
    $entry2 = WorkEntryMother::random(['userId' => $userId->toString()]);
    $this->repositorySave->save($entry1);
    $this->repositorySave->save($entry2);

    $criteria = CriteriaMother::withOneFilter('userId', 'eq', $userId->toString());

    // WHEN
    $result = $this->repositoryFind->searchAllByCriteria($criteria);

    // THEN
    self::assertCount(2, $result);
}
```

## Accessing Container Services

```php
$entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
$someService = self::getContainer()->get(SomeService::class);
```

## Common Assertions

```php
self::assertEquals($expected, $actual);
self::assertNotNull($result);
self::assertNull($result);
self::assertCount(2, $collection);
self::assertInstanceOf(Entity::class, $result);
```

## PHP Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- Use `#[Test]` attribute
- Use `final class`
- Always call `parent::setUp()` first
- Use real repositories, not mocks
- Each test is isolated via transaction rollback
