---
paths:
  - tests/Unit/**/Mother*.php
  - tests/Unit/**/*Mother.php
  - tests/Utils/**/*.php
---

# Mother Objects & Factories

This rule applies to Mother Objects and Factory files used for test data generation.

## Purpose

Mother Objects generate consistent, random test data. Factories persist entities to the database for functional tests.

## MotherCreator (Base)

The base class for generating primitive values using Faker:

```php
<?php

declare(strict_types=1);

namespace Tests\Utils\Mother;

use Faker\Factory;
use Faker\Generator;

final class MotherCreator
{
    private static ?Generator $faker = null;

    public static function random(): Generator
    {
        return self::$faker = self::$faker ?? Factory::create('es_ES');
    }

    public static function id(): string
    {
        return self::random()->uuid();
    }

    public static function email(): string
    {
        return self::random()->email();
    }

    public static function name(): string
    {
        return self::random()->name();
    }

    public static function password(): string
    {
        return self::random()->password(minLength: 8, maxLength: 16);
    }

    public static function dateTime(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromMutable(self::random()->dateTime());
    }

    public static function dateTimeFormat(string $format = \DateTimeInterface::ATOM): string
    {
        return self::dateTime()->format($format);
    }

    public static function text(int $maxChars = 200): string
    {
        return self::random()->text($maxChars);
    }

    public static function numberBetween(int $min = 0, int $max = 100): int
    {
        return self::random()->numberBetween($min, $max);
    }
}
```

## Entity Mother Example

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\{Context}\Domain\Entities;

use App\{Context}\Application\Commands\Create{Entity}\Create{Entity}Command;
use App\{Context}\Domain\Entities\{Entity};
use Tests\Utils\Mother\MotherCreator;

final class {Entity}Mother
{
    /**
     * Creates entity with all fields (for reconstruction via make()).
     *
     * @param array<string, mixed> $overrides
     */
    public static function random(array $overrides = []): {Entity}
    {
        $createdAt = MotherCreator::dateTime();
        $updatedAt = $createdAt->modify('+8 hours');

        $randomData = [
            'id' => MotherCreator::id(),
            'name' => MotherCreator::name(),
            'email' => MotherCreator::email(),
            'password' => MotherCreator::password(),
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
            'deletedAt' => null,
        ];

        $finalData = array_merge($randomData, $overrides);

        return {Entity}::make(
            id: $finalData['id'],
            name: $finalData['name'],
            email: $finalData['email'],
            password: $finalData['password'],
            createdAt: $finalData['createdAt'],
            updatedAt: $finalData['updatedAt'],
            deletedAt: $finalData['deletedAt'],
        );
    }

    /**
     * Creates entity in "new" state (for creation via create()).
     *
     * @param array<string, mixed> $overrides
     */
    public static function create(array $overrides = []): {Entity}
    {
        $randomData = [
            'id' => MotherCreator::id(),
            'name' => MotherCreator::name(),
            'email' => MotherCreator::email(),
            'password' => MotherCreator::password(),
            'createdAt' => MotherCreator::dateTime(),
        ];

        $finalData = array_merge($randomData, $overrides);

        return {Entity}::create(
            id: $finalData['id'],
            name: $finalData['name'],
            email: $finalData['email'],
            password: $finalData['password'],
            createdAt: $finalData['createdAt'],
        );
    }

    /**
     * Creates entity from Command (for handler tests).
     */
    public static function fromCreate{Entity}Command(Create{Entity}Command $command): {Entity}
    {
        return self::create([
            'id' => $command->id,
            'name' => $command->name,
            'email' => $command->email,
            'password' => $command->plainPassword,
            'createdAt' => $command->createdAt,
        ]);
    }

    /**
     * Creates a specific named entity (for auth tests).
     */
    public static function admin(): {Entity}
    {
        return self::create([
            'name' => 'admin',
            'email' => 'admin@app.es',
            'password' => 't@hi$si_smypAs5w0rD',
        ]);
    }
}
```

## Command Mother Example

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\{Context}\Application\Commands\Create{Entity};

use App\{Context}\Application\Commands\Create{Entity}\Create{Entity}Command;
use Tests\Utils\Mother\MotherCreator;

final class Create{Entity}CommandMother
{
    /**
     * @param array<string, mixed> $overrides
     */
    public static function random(array $overrides = []): Create{Entity}Command
    {
        $randomData = [
            'id' => MotherCreator::id(),
            'name' => MotherCreator::name(),
            'email' => MotherCreator::email(),
            'password' => MotherCreator::password(),
            'createdAt' => MotherCreator::dateTime(),
        ];

        $finalData = array_merge($randomData, $overrides);

        return new Create{Entity}Command(
            id: $finalData['id'],
            name: $finalData['name'],
            email: $finalData['email'],
            plainPassword: $finalData['password'],
            createdAt: $finalData['createdAt'],
        );
    }
}
```

## Mother Rules

- **Entity Mother Location**: `tests/Unit/{Context}/Domain/Entities/`
- **Command Mother Location**: `tests/Unit/{Context}/Application/Commands/{Action}{Entity}/`
- **Naming**: `{Entity}Mother.php`, `{Action}{Entity}CommandMother.php`
- **Class Modifier**: Use `final class`

## Mother Patterns

### random() Method

Creates entity with all fields populated:
```php
public static function random(array $overrides = []): Entity
{
    $randomData = [/* defaults */];
    $finalData = array_merge($randomData, $overrides);
    return Entity::make(...$finalData);
}
```

### create() Method

Creates entity in "new" state:
```php
public static function create(array $overrides = []): Entity
{
    $randomData = [/* defaults without updatedAt */];
    $finalData = array_merge($randomData, $overrides);
    return Entity::create(...$finalData);
}
```

### fromCommand() Method

Creates entity from a Command (for handler tests):
```php
public static function fromCreateEntityCommand(CreateEntityCommand $cmd): Entity
{
    return self::create([
        'id' => $cmd->id,
        'name' => $cmd->name,
    ]);
}
```

### Named Entities

For specific test scenarios:
```php
public static function admin(): User { return self::create([...]); }
public static function deleted(): User { return self::random(['deletedAt' => new \DateTimeImmutable()]); }
```

## Factory (Persistence)

Factories persist entities for functional tests:

```php
<?php

declare(strict_types=1);

namespace Tests\Utils\Factory\{Entity};

use App\{Context}\Domain\Entities\{Entity};
use App\Shared\Domain\Aggregate\AggregateRoot;
use Tests\Utils\Factory\AbstractFactory;

final class {Entity}Factory extends AbstractFactory
{
    public function createOne(AggregateRoot $entity): void
    {
        if (false === $entity instanceof {Entity}) {
            throw new \Exception('Invalid entity type');
        }

        $this->persistence->persist($entity);
    }

    public function createMany(int $count = 5): void
    {
        for ($i = 0; $i < $count; ++$i) {
            $this->createOne({Entity}Mother::random());
        }
    }
}
```

## Factory Rules

- **Location**: `tests/Utils/Factory/{Entity}/`
- **Naming**: `{Entity}Factory.php`
- **Base Class**: `Tests\Utils\Factory\AbstractFactory`

## Usage in Tests

### Unit Tests

```php
$command = CreateUserCommandMother::random();
$user = UserMother::fromCreateUserCommand($command);
```

### Functional Tests

```php
protected function setUp(): void
{
    parent::setUp();
    $this->userFactory = new UserFactory($this->factoryPersistence());
}

#[Test]
public function itShouldFindUser(): void
{
    $user = UserMother::random();
    $this->userFactory->createOne($user);
    // ... test
}
```

## Naming Conventions

| Type | Pattern | Example |
|------|---------|---------|
| Entity Mother | `{Entity}Mother` | `UserMother` |
| Command Mother | `{Action}{Entity}CommandMother` | `CreateUserCommandMother` |
| Query Mother | `{Action}{Entity}QueryMother` | `FindUserByIdQueryMother` |
| Factory | `{Entity}Factory` | `UserFactory` |

## Methods Summary

| Method | Purpose | Uses |
|--------|---------|------|
| `random()` | Full entity with all fields | `Entity::make()` |
| `create()` | New entity (no updatedAt) | `Entity::create()` |
| `fromCommand()` | From Command object | Handler tests |
| `admin()` / named | Specific scenarios | Auth tests |

## PHP Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- Use `final class`
- Use `array<string, mixed>` for overrides
- Use `array_merge()` for applying overrides
- Use Faker via `MotherCreator::random()`
