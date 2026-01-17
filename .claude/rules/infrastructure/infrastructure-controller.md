---
paths: src/*/Infrastructure/Api/**/*.php
---

# Infrastructure Controllers & Request DTOs

This rule applies to Controller and Request DTO files in the Infrastructure layer.

## Purpose

Controllers handle HTTP requests, validate input, dispatch commands/queries, and return responses. Request DTOs encapsulate and validate incoming data.

## Controller Example

```php
<?php

declare(strict_types=1);

namespace App\{Context}\Infrastructure\Api\Create{Entity};

use App\Shared\Api\BaseController;
use App\{Context}\Domain\Exceptions\{Entity}NotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

final class Create{Entity}Controller extends BaseController
{
    public function __invoke(
        #[MapRequestPayload] Create{Entity}Request $request,
    ): Response {
        $this->commandBus->command($request->toCreate{Entity}Command());

        return new Response(status: Response::HTTP_CREATED);
    }

    /**
     * @return array<class-string<\Throwable>, int>
     */
    protected function exceptions(): array
    {
        return [
            {Entity}NotFoundException::class => Response::HTTP_NOT_FOUND,
        ];
    }
}
```

## Request DTO Example

```php
<?php

declare(strict_types=1);

namespace App\{Context}\Infrastructure\Api\Create{Entity};

use App\{Context}\Application\Commands\Create{Entity}\Create{Entity}Command;
use Symfony\Component\Validator\Constraints as Assert;

final class Create{Entity}Request
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id,

        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 100)]
        public string $name,

        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,

        #[\SensitiveParameter]
        #[Assert\NotBlank]
        #[Assert\Length(min: 8)]
        public string $plainPassword,

        #[Assert\NotBlank]
        #[Assert\DateTime(format: \DateTimeInterface::ATOM)]
        public string $createdAt,
    ) {
    }

    public function toCreate{Entity}Command(): Create{Entity}Command
    {
        return new Create{Entity}Command(
            $this->id,
            $this->name,
            $this->email,
            $this->plainPassword,
            new \DateTimeImmutable($this->createdAt),
        );
    }
}
```

## Controller Rules

- **Location**: `Infrastructure/Api/{Action}{Entity}/`
- **Naming**: `{Action}{Entity}Controller.php` (e.g., `CreateUserController.php`)
- **Base Class**: Extend `App\Shared\Api\BaseController`
- **Class Modifier**: Use `final class`
- **Pattern**: Single action via `__invoke()` method

## Controller Patterns

### Create (POST)

```php
public function __invoke(
    #[MapRequestPayload] CreateUserRequest $request,
): Response {
    $this->commandBus->command($request->toCreateUserCommand());

    return new Response(status: Response::HTTP_CREATED);
}
```

### Read by ID (GET)

```php
public function __invoke(string $id): Response
{
    $response = $this->query(new FindUserByIdQuery($id));

    return new JsonResponse($response, Response::HTTP_OK);
}
```

### Update (PUT)

```php
public function __invoke(
    string $id,
    #[MapRequestPayload] UpdateUserRequest $request,
): Response {
    $this->commandBus->command($request->toUpdateUserCommand($id));

    return new Response(status: Response::HTTP_OK);
}
```

### Delete (DELETE)

```php
public function __invoke(string $id): Response
{
    $this->commandBus->command(new DeleteUserCommand($id));

    return new Response(status: Response::HTTP_NO_CONTENT);
}
```

### List (GET)

```php
public function __invoke(): Response
{
    $user = $this->authenticatedUserProvider->currentUser();
    $response = $this->query(new ListWorkEntryQuery($user->id()->toString()));

    return new JsonResponse($response, Response::HTTP_OK);
}
```

### Action with URL Parameter (PATCH)

```php
public function __invoke(
    string $id,
    #[MapRequestPayload] ClockInRequest $request,
): Response {
    $user = $this->authenticatedUserProvider->currentUser();
    $this->commandBus->command(
        $request->toClockInCommand(
            workEntryId: $id,
            userId: $user->id()->toString()
        )
    );

    return new Response(status: Response::HTTP_OK);
}
```

## Exception Mapping

Map domain exceptions to HTTP status codes:
```php
protected function exceptions(): array
{
    return [
        UserNotFoundException::class => Response::HTTP_NOT_FOUND,           // 404
        UserAlreadyExistsException::class => Response::HTTP_CONFLICT,       // 409
        WorkEntryNotBelongToUserException::class => Response::HTTP_FORBIDDEN, // 403
        WorkEntryAlreadyClockedInException::class => Response::HTTP_CONFLICT, // 409
    ];
}
```

## Request DTO Rules

- **Location**: Same folder as Controller
- **Naming**: `{Action}{Entity}Request.php` (e.g., `CreateUserRequest.php`)
- **Class Modifier**: Use `final class`
- **Properties**: Public with validation attributes
- **Conversion Method**: `to{Action}{Entity}Command()` or `to{Action}{Entity}Query()`

## Validation Attributes

Common Symfony validation constraints:

| Constraint | Usage |
|------------|-------|
| `#[Assert\NotBlank]` | Required field |
| `#[Assert\Uuid]` | Valid UUID format |
| `#[Assert\Email]` | Valid email format |
| `#[Assert\Length(min, max)]` | String length |
| `#[Assert\DateTime(format)]` | Date format |
| `#[Assert\NotNull]` | Field cannot be null |
| `#[Assert\Positive]` | Positive number |

## Routes Configuration

```yaml
# Infrastructure/Api/routes.yaml
CreateUser:
  path: /
  controller: App\Template\User\Infrastructure\Api\CreateUser\CreateUserController
  methods: [POST]

FindUserById:
  path: /{id}
  controller: App\Template\User\Infrastructure\Api\FindUserById\FindUserByIdController
  methods: [GET]

UpdateUser:
  path: /{id}
  controller: App\Template\User\Infrastructure\Api\UpdateUser\UpdateUserController
  methods: [PUT]

DeleteUser:
  path: /{id}
  controller: App\Template\User\Infrastructure\Api\DeleteUser\DeleteUserController
  methods: [DELETE]

# Action routes
TimeTrackingClockIn:
  path: /{id}/clock-in
  controller: App\Template\TimeTracking\Infrastructure\Api\ClockIn\ClockInController
  methods: [PATCH]
```

## Naming Conventions

| Type | Pattern | Example |
|------|---------|---------|
| Controller | `{Action}{Entity}Controller` | `CreateUserController` |
| Request DTO | `{Action}{Entity}Request` | `CreateUserRequest` |
| Route name | `{Action}{Entity}` | `CreateUser` |
| Conversion method | `to{Action}{Entity}Command` | `toCreateUserCommand()` |

## PHP Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- Use `final class` for Controllers and Request DTOs
- Use `#[MapRequestPayload]` for automatic validation
- Use `#[\SensitiveParameter]` for passwords and sensitive data
- Constructor property promotion with trailing commas
