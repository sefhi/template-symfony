---
paths: tests/Functional/**/*.php
---

# Functional Tests (API Tests)

This rule applies to Functional/API test files.

## Purpose

Functional tests verify API endpoints end-to-end. They test the full request/response cycle with a real database. An admin user is automatically authenticated.

## Controller Test Example

```php
<?php

declare(strict_types=1);

namespace Tests\Functional\{Context}\Infrastructure\Api\Create{Entity};

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\Functional\BaseApiTestCase;
use Tests\Utils\Mother\MotherCreator;

final class Create{Entity}ControllerTest extends BaseApiTestCase
{
    #[Test]
    public function itShouldCreate{Entity}(): void
    {
        // GIVEN
        $payload = [
            'id' => MotherCreator::id(),
            'name' => MotherCreator::name(),
            'email' => MotherCreator::email(),
            'plainPassword' => MotherCreator::password(),
            'createdAt' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
        ];

        // WHEN
        $this->client()->request(
            'POST',
            '/api/users',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload)
        );

        // THEN
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }
}
```

## Rules

- **Location**: `tests/Functional/{Context}/Infrastructure/Api/{Action}{Entity}/`
- **Naming**: `{Action}{Entity}ControllerTest.php`
- **Base Class**: `Tests\Functional\BaseApiTestCase`
- **Class Modifier**: Use `final class`

## Base Test Case

The base class provides:
- HTTP client
- Database transaction (rollback after each test)
- Automatic admin user authentication
- Factory persistence for creating test data

```php
abstract class BaseApiTestCase extends WebTestCase
{
    private ?KernelBrowser $client;
    private EntityManagerInterface $entityManager;
    private PersistenceInterface $factoryPersistence;
    private User $userLogged;

    protected function setUp(): void
    {
        self::ensureKernelShutdown();
        $this->client = self::createClient();
        $this->entityManager = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->factoryPersistence = new DoctrinePersistence($this->entityManager);
        $this->entityManager->beginTransaction();
        $this->ensureAuthenticatedInTest();
    }

    protected function tearDown(): void
    {
        $this->entityManager->rollback();
    }

    public function client(): KernelBrowser { return $this->client; }
    public function factoryPersistence(): PersistenceInterface { return $this->factoryPersistence; }
    protected function getUserLogged(): User { return $this->userLogged; }
}
```

## HTTP Request Patterns

### POST Request (Create)

```php
$this->client()->request(
    'POST',
    '/api/users',
    [],                                    // parameters
    [],                                    // files
    ['CONTENT_TYPE' => 'application/json'], // headers
    json_encode($payload)                   // body
);

self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
```

### GET Request (Read)

```php
$this->client()->request('GET', '/api/users/' . $userId);

self::assertResponseIsSuccessful();
self::assertResponseStatusCodeSame(Response::HTTP_OK);

$response = json_decode($this->client()->getResponse()->getContent(), true);
self::assertEquals($userId, $response['id']);
```

### PUT Request (Update)

```php
$this->client()->request(
    'PUT',
    '/api/users/' . $userId,
    [],
    [],
    ['CONTENT_TYPE' => 'application/json'],
    json_encode($payload)
);

self::assertResponseStatusCodeSame(Response::HTTP_OK);
```

### DELETE Request

```php
$this->client()->request('DELETE', '/api/users/' . $userId);

self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
```

### PATCH Request (Actions)

```php
$this->client()->request(
    'PATCH',
    '/api/work-entries/' . $workEntryId . '/clock-in',
    [],
    [],
    ['CONTENT_TYPE' => 'application/json'],
    json_encode(['startDate' => $startDate])
);

self::assertResponseStatusCodeSame(Response::HTTP_OK);
```

## Creating Test Data

Use Factories to persist entities before testing:

```php
final class FindUserByIdControllerTest extends BaseApiTestCase
{
    private UserFactory $userFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userFactory = new UserFactory($this->factoryPersistence());
    }

    #[Test]
    public function itShouldFindUserById(): void
    {
        // GIVEN
        $user = UserMother::random();
        $this->userFactory->createOne($user);

        // WHEN
        $this->client()->request('GET', '/api/users/' . $user->id()->toString());

        // THEN
        self::assertResponseIsSuccessful();
        $response = json_decode($this->client()->getResponse()->getContent(), true);
        self::assertEquals($user->id()->toString(), $response['id']);
    }
}
```

## Testing Error Responses

### Not Found (404)

```php
#[Test]
public function itShouldReturnNotFoundWhenUserDoesNotExist(): void
{
    // GIVEN
    $nonExistentId = MotherCreator::id();

    // WHEN
    $this->client()->request('GET', '/api/users/' . $nonExistentId);

    // THEN
    self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
}
```

### Validation Error (422)

```php
#[Test]
public function itShouldReturnValidationErrorForInvalidEmail(): void
{
    // GIVEN
    $payload = [
        'id' => MotherCreator::id(),
        'name' => MotherCreator::name(),
        'email' => 'invalid-email',
        'plainPassword' => MotherCreator::password(),
        'createdAt' => (new \DateTimeImmutable())->format(\DateTimeInterface::ATOM),
    ];

    // WHEN
    $this->client()->request(
        'POST',
        '/api/users',
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode($payload)
    );

    // THEN
    self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
}
```

### Forbidden (403)

```php
#[Test]
public function itShouldReturnForbiddenWhenWorkEntryBelongsToAnotherUser(): void
{
    // GIVEN
    $anotherUser = UserMother::random();
    $this->userFactory->createOne($anotherUser);

    $workEntry = WorkEntryMother::random(['userId' => $anotherUser->id()->toString()]);
    $this->workEntryFactory->createOne($workEntry);

    // WHEN
    $this->client()->request(
        'PATCH',
        '/api/work-entries/' . $workEntry->id()->toString() . '/clock-in',
        [],
        [],
        ['CONTENT_TYPE' => 'application/json'],
        json_encode(['startDate' => MotherCreator::dateTimeFormat()])
    );

    // THEN
    self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
}
```

## Common Assertions

```php
// Response status
self::assertResponseIsSuccessful();
self::assertResponseStatusCodeSame(Response::HTTP_OK);
self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
self::assertResponseStatusCodeSame(Response::HTTP_CONFLICT);

// Response content
$response = json_decode($this->client()->getResponse()->getContent(), true);
self::assertEquals($expected, $response['field']);
self::assertArrayHasKey('id', $response);
self::assertCount(2, $response['items']);
```

## Accessing Logged User

```php
#[Test]
public function itShouldReturnCurrentUser(): void
{
    // GIVEN - User is already logged (admin)
    $loggedUser = $this->getUserLogged();

    // WHEN
    $this->client()->request('GET', '/api/users/me');

    // THEN
    self::assertResponseIsSuccessful();
    $response = json_decode($this->client()->getResponse()->getContent(), true);
    self::assertEquals($loggedUser->id()->toString(), $response['id']);
}
```

## PHP Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- Use `#[Test]` attribute
- Use `final class`
- Always call `parent::setUp()` first
- Use `$this->client()` for HTTP requests
- Use Factories for test data setup
