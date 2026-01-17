---
paths: tests/Unit/**/*.php
---

# Unit Tests

This rule applies to Unit test files.

## Purpose

Unit tests verify handlers, services, and domain logic in complete isolation. All dependencies are mocked.

## Handler Test Example

```php
<?php

declare(strict_types=1);

namespace Tests\Unit\{Context}\Application\Commands\Create{Entity};

use App\{Context}\Application\Commands\Create{Entity}\Create{Entity}Handler;
use App\{Context}\Domain\Repositories\{Entity}SaveRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\{Context}\Domain\Entities\{Entity}Mother;

final class Create{Entity}HandlerTest extends TestCase
{
    private {Entity}SaveRepository|MockObject $repository;
    private Create{Entity}Handler $handler;

    protected function setUp(): void
    {
        $this->repository = $this->createMock({Entity}SaveRepository::class);

        $this->handler = new Create{Entity}Handler(
            $this->repository,
        );
    }

    #[Test]
    public function itShouldCreate{Entity}(): void
    {
        // GIVEN
        $command = Create{Entity}CommandMother::random();
        ${entity}Expected = {Entity}Mother::fromCreate{Entity}Command($command);

        // WHEN
        $this->repository
            ->expects(self::once())
            ->method('save')
            ->with(${entity}Expected);

        // THEN
        ($this->handler)($command);
    }
}
```

## Rules

- **Location**: `tests/Unit/{Context}/{Layer}/{UseCase}/`
- **Naming**: `{ClassName}Test.php`
- **Base Class**: `PHPUnit\Framework\TestCase`
- **Class Modifier**: Use `final class`

## Test Structure

### Properties

Declare mocks with union types:
```php
private UserSaveRepository|MockObject $userRepository;
private PasswordHasher|MockObject $passwordHasher;
private CreateUserHandler $handler;
```

### setUp Method

Initialize mocks and inject dependencies:
```php
protected function setUp(): void
{
    $this->userRepository = $this->createMock(UserSaveRepository::class);
    $this->passwordHasher = $this->createMock(PasswordHasher::class);

    $this->handler = new CreateUserHandler(
        $this->userRepository,
        $this->passwordHasher,
    );
}
```

### Test Methods

Use `#[Test]` attribute and GIVEN-WHEN-THEN:
```php
#[Test]
public function itShouldCreateUser(): void
{
    // GIVEN - Prepare data
    $command = CreateUserCommandMother::random();
    $userExpected = UserMother::fromCreateUserCommand($command);
    $passwordExpected = 'hashedpassword';

    // WHEN - Configure mocks
    $this->passwordHasher
        ->expects(self::once())
        ->method('hashPlainPassword')
        ->with($userExpected, $command->plainPassword)
        ->willReturn($passwordExpected);

    $this->userRepository
        ->expects(self::once())
        ->method('save')
        ->with($userExpected->withPasswordHashed($passwordExpected));

    // THEN - Execute
    ($this->handler)($command);
}
```

## Mock Expectations

### Basic Mock

```php
$this->repository
    ->expects(self::once())
    ->method('save')
    ->with($entity);
```

### Mock with Return Value

```php
$this->repository
    ->expects(self::once())
    ->method('findById')
    ->with($id)
    ->willReturn($entity);
```

### Mock with Exception

```php
$this->repository
    ->expects(self::once())
    ->method('findById')
    ->with($id)
    ->willReturn(null);  // For "not found" cases
```

### Expectation Counts

```php
->expects(self::once())      // Exactly 1 time
->expects(self::exactly(3))  // Exactly 3 times
->expects(self::any())       // Any number of times
->expects(self::never())     // Never called
```

## Testing Exceptions

```php
#[Test]
public function itShouldThrowUserNotFoundExceptionWhenUserDoesNotExist(): void
{
    // GIVEN
    $userId = Uuid::fromString(MotherCreator::id());

    // WHEN
    $this->userFindRepository
        ->expects(self::once())
        ->method('findById')
        ->with($userId)
        ->willReturn(null);

    // THEN
    $this->expectException(UserNotFoundException::class);
    ($this->service)($userId);
}
```

## Query Handler Test Example

```php
#[Test]
public function itShouldFindUserById(): void
{
    // GIVEN
    $query = new FindUserByIdQuery(MotherCreator::id());
    $user = UserMother::random(['id' => $query->id]);
    $userResponseExpected = UserResponse::fromUser($user);

    // WHEN
    $this->ensureExistsUserByIdService
        ->expects(self::once())
        ->method('__invoke')
        ->with(Uuid::fromString($query->id))
        ->willReturn($user);

    // THEN
    $result = ($this->handler)($query);

    self::assertInstanceOf(UserResponse::class, $result);
    self::assertEquals($userResponseExpected, $result);
}
```

## Domain Service Test Example

```php
final class EnsureExistsUserByIdServiceTest extends TestCase
{
    private UserFindRepository|MockObject $userFindRepository;
    private EnsureExistsUserByIdService $service;

    protected function setUp(): void
    {
        $this->userFindRepository = $this->createMock(UserFindRepository::class);
        $this->service = new EnsureExistsUserByIdService($this->userFindRepository);
    }

    #[Test]
    public function itShouldReturnUserWhenExists(): void
    {
        // GIVEN
        $userId = Uuid::fromString(MotherCreator::id());
        $userExpected = UserMother::random(['id' => $userId->toString()]);

        // WHEN
        $this->userFindRepository
            ->expects(self::once())
            ->method('findById')
            ->with($userId)
            ->willReturn($userExpected);

        // THEN
        $result = ($this->service)($userId);
        self::assertEquals($userExpected, $result);
    }

    #[Test]
    public function itShouldThrowExceptionWhenUserNotFound(): void
    {
        // GIVEN
        $userId = Uuid::fromString(MotherCreator::id());

        // WHEN
        $this->userFindRepository
            ->expects(self::once())
            ->method('findById')
            ->with($userId)
            ->willReturn(null);

        // THEN
        $this->expectException(UserNotFoundException::class);
        ($this->service)($userId);
    }
}
```

## Common Assertions

```php
self::assertEquals($expected, $actual);
self::assertInstanceOf(UserResponse::class, $result);
self::assertTrue($condition);
self::assertFalse($condition);
self::assertNull($value);
self::assertNotNull($value);
self::assertCount(3, $collection);
```

## PHP Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- Use `#[Test]` attribute (not `/** @test */`)
- Use `final class`
- Type properties with `|MockObject` union
- Invoke handlers: `($this->handler)($command)`
- Always use Mother objects for test data
