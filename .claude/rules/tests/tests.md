---
paths: tests/**/*.php
---

# Test Rules & Conventions

This rule applies to all test files in the project.

## Fundamental Principles

1. **GIVEN-WHEN-THEN**: All tests follow this structure in comments
2. **Mother Objects**: Use Mothers for test data generation
3. **Isolation**: Unit tests mock all dependencies
4. **Transaction Rollback**: Integration/Functional tests rollback after each test

## Test Structure

```
tests/
├── Unit/                          # Tests without database
│   └── {Context}/
│       ├── Application/
│       │   ├── Commands/{Action}{Entity}/
│       │   │   ├── {Action}{Entity}HandlerTest.php
│       │   │   └── {Action}{Entity}CommandMother.php
│       │   └── Queries/{Action}{Entity}/
│       │       └── {Action}{Entity}HandlerTest.php
│       └── Domain/
│           ├── Entities/
│           │   └── {Entity}Mother.php
│           └── Services/
│               └── {Service}Test.php
│
├── Integration/                   # Tests with database (Doctrine)
│   └── {Context}/
│       └── Infrastructure/
│           └── Persistence/
│               └── Repository/
│                   └── Doctrine{Entity}RepositoryTest.php
│
├── Functional/                    # API endpoint tests
│   └── {Context}/
│       └── Infrastructure/
│           └── Api/
│               └── {Action}{Entity}/
│                   └── {Action}{Entity}ControllerTest.php
│
└── Utils/                         # Helpers and Factories
    ├── Factory/
    │   ├── AbstractFactory.php
    │   └── {Entity}/
    │       └── {Entity}Factory.php
    └── Mother/
        └── MotherCreator.php
```

## Test Types

| Type | Base Class | Database | Purpose |
|------|------------|----------|---------|
| Unit | `TestCase` | No | Test handlers, services, entities in isolation |
| Integration | `BaseDoctrineIntegrationTestCase` | Yes | Test repository implementations |
| Functional | `BaseApiTestCase` | Yes | Test API endpoints end-to-end |

For detailed conventions, see the specific rule files:
- `tests-unit.md` - Unit tests
- `tests-integration.md` - Integration tests
- `tests-functional.md` - Functional/API tests
- `tests-mother.md` - Mother Objects and Factories

## Naming Conventions

### Test Classes

| Type | Pattern | Example |
|------|---------|---------|
| Handler Test | `{Action}{Entity}HandlerTest` | `CreateUserHandlerTest` |
| Service Test | `{Service}Test` | `EnsureExistsUserByIdServiceTest` |
| Repository Test | `Doctrine{Entity}RepositoryTest` | `DoctrineBookSaveRepositoryTest` |
| Controller Test | `{Action}{Entity}ControllerTest` | `CreateUserControllerTest` |
| Entity Mother | `{Entity}Mother` | `UserMother` |
| Command Mother | `{Action}{Entity}CommandMother` | `CreateUserCommandMother` |

### Test Methods

Always use `#[Test]` attribute and descriptive names:
```php
#[Test]
public function itShouldCreateUser(): void { }

#[Test]
public function itShouldThrowUserNotFoundExceptionWhenUserDoesNotExist(): void { }
```

Pattern: `itShould` + `Action` + `Condition` (optional)

## GIVEN-WHEN-THEN Structure

```php
#[Test]
public function itShouldCreateUser(): void
{
    // GIVEN
    $command = CreateUserCommandMother::random();
    $userExpected = UserMother::fromCreateUserCommand($command);

    // WHEN
    $this->repository
        ->expects(self::once())
        ->method('save')
        ->with($userExpected);

    // THEN
    ($this->handler)($command);
}
```

## Running Tests

```bash
# All tests
make test

# With coverage
make test/coverage

# Single file
docker compose exec webserver php bin/phpunit tests/Unit/Path/To/TestFile.php

# Single method
docker compose exec webserver php bin/phpunit --filter itShouldCreateUser

# By module
docker compose exec webserver php bin/phpunit tests/Unit/Template/User/
```

## PHP Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- Use `#[Test]` attribute (PHPUnit 10+)
- Use `final class` for test classes
- Use typed properties with `|MockObject` for mocks
- Constructor property promotion where applicable
