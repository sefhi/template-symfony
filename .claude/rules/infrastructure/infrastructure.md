---
paths: src/*/Infrastructure/**/*.php
---

# Infrastructure Layer Rules & Conventions

This rule applies to all PHP files in the Infrastructure layer of bounded contexts.

## Fundamental Principles

1. **Framework Integration**: Infrastructure adapts external frameworks to domain interfaces
2. **Implements Domain Ports**: Concrete implementations of repository interfaces
3. **External Services**: HTTP clients, message queues, file systems, etc.
4. **Delivery Mechanism**: Controllers handle HTTP requests and responses

## Allowed Dependencies

The Infrastructure layer CAN import from:
- `Symfony\*` - Framework components
- `Doctrine\*` - ORM for persistence
- `App\{Context}\Domain\*` - Domain layer of same context
- `App\{Context}\Application\*` - Application layer for Commands/Queries
- `App\Shared\*` - Shared components

## Infrastructure Layer Structure

```
Infrastructure/
├── Api/                              # HTTP layer
│   ├── {Action}{Entity}/
│   │   ├── {Action}{Entity}Controller.php
│   │   └── {Action}{Entity}Request.php
│   └── routes.yaml
├── Persistence/                      # Database layer
│   ├── Repositories/
│   │   ├── Doctrine{Entity}FindRepository.php
│   │   └── Doctrine{Entity}SaveRepository.php
│   └── Doctrine/
│       └── Mapping/
│           ├── Entities/
│           │   └── {Entity}.orm.xml
│           └── ValueObjects/
│               └── {ValueObject}.orm.xml
└── Security/                         # Security implementations
    ├── UserProvider.php
    ├── UserAdapter.php
    └── Symfony{Interface}Implementation.php
```

## Key Components

### Controllers

Extend `BaseController`, use `__invoke()` pattern:
```php
final class CreateUserController extends BaseController
{
    public function __invoke(#[MapRequestPayload] CreateUserRequest $request): Response
    {
        $this->commandBus->command($request->toCreateUserCommand());
        return new Response(status: Response::HTTP_CREATED);
    }
}
```

### Request DTOs

Validation via PHP 8 attributes:
```php
final class CreateUserRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid]
        public string $id,
    ) {}

    public function toCreateUserCommand(): CreateUserCommand { ... }
}
```

### Doctrine Repositories

Implement domain interfaces:
```php
final readonly class DoctrineUserFindRepository extends DoctrineRepository
    implements UserFindRepository
{
    public function findById(UuidInterface $id): ?User { ... }
}
```

For detailed conventions, see the specific rule files:
- `infrastructure-controller.md` - Controllers and Request DTOs
- `infrastructure-repository.md` - Doctrine Repositories

## Naming Conventions Summary

| Type | Pattern | Example |
|------|---------|---------|
| Controller | `{Action}{Entity}Controller` | `CreateUserController` |
| Request DTO | `{Action}{Entity}Request` | `CreateUserRequest` |
| Find Repository | `Doctrine{Entity}FindRepository` | `DoctrineUserFindRepository` |
| Save Repository | `Doctrine{Entity}SaveRepository` | `DoctrineUserSaveRepository` |
| Route name | `{Action}{Entity}` | `CreateUser` |

## Routes Configuration

Each context has its own `routes.yaml`:
```yaml
CreateUser:
  path: /
  controller: App\Template\User\Infrastructure\Api\CreateUser\CreateUserController
  methods: [POST]

FindUserById:
  path: /{id}
  controller: App\Template\User\Infrastructure\Api\FindUserById\FindUserByIdController
  methods: [GET]
```

## PHP Standards

- `declare(strict_types=1)` in ALL files
- PSR-12 coding standard
- Use `final class` or `final readonly class`
- Constructor property promotion with trailing commas
- Use `readonly` for immutable properties
