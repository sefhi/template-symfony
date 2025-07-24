<?php

declare(strict_types=1);

namespace Tests\Unit\Sesame\User\Application\Commands\CreateUser;

use App\Sesame\User\Application\Commands\CreateUser\CreateUserHandler;
use App\Sesame\User\Domain\Repositories\UserSaveRepository;
use App\Sesame\User\Domain\Security\PasswordHasher;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Sesame\User\Domain\Entities\UserMother;

final class CreateUserHandlerTest extends TestCase
{
    private UserSaveRepository|MockObject $userRepository;
    private PasswordHasher|MockObject $passwordHasher;
    private CreateUserHandler $handler;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserSaveRepository::class);
        $this->passwordHasher = $this->createMock(PasswordHasher::class);

        $this->handler = new CreateUserHandler(
            $this->userRepository,
            $this->passwordHasher,
        );
    }

    #[Test]
    public function itShouldCreateUser(): void
    {
        // GIVEN

        $command          = CreateUserCommandMother::random();
        $userExpected     = UserMother::fromCreateUserCommand($command);
        $passwordExpected = 'passwordhashed';

        // WHEN

        $this->passwordHasher
            ->expects(self::once())
            ->method('hashPlainPassword')
            ->with($userExpected, $command->plainPassword)
            ->willReturn($passwordExpected);

        $userExpected = $userExpected->withPasswordHashed($passwordExpected);

        $this->userRepository
            ->expects(self::once())
            ->method('save')
            ->with($userExpected);

        // THEN

        ($this->handler)($command);
    }
}
