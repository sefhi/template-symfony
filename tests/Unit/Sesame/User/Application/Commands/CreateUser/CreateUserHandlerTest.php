<?php

declare(strict_types=1);

namespace Tests\Unit\Sesame\User\Application\Commands\CreateUser;

use App\Sesame\User\Application\Commands\CreateUser\CreateUserHandler;
use App\Sesame\User\Domain\Repositories\UserRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Sesame\User\Domain\Entities\UserMother;

final class CreateUserHandlerTest extends TestCase
{
    private UserRepository|MockObject $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);
        // TODO service password hasher
    }

    #[Test]
    public function itShouldCreateUser(): void
    {
        // GIVEN

        $command      = CreateUserCommandMother::random();
        $userExpected = UserMother::fromCreateUserCommand($command);

        // WHEN

        $this->userRepository
            ->expects(self::once())
            ->method('save')
            ->with($userExpected);

        $handler = new CreateUserHandler(
            $this->userRepository,
        );

        // THEN

        $handler($command);
    }
}
