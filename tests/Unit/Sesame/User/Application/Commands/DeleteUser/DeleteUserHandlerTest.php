<?php

declare(strict_types=1);

namespace Tests\Unit\Sesame\User\Application\Commands\DeleteUser;

use App\Sesame\User\Application\Commands\DeleteUser\DeleteUserCommand;
use App\Sesame\User\Application\Commands\DeleteUser\DeleteUserHandler;
use App\Sesame\User\Domain\Entities\User;
use App\Sesame\User\Domain\Exceptions\UserNotFoundException;
use App\Sesame\User\Domain\Repositories\UserFindRepository;
use App\Sesame\User\Domain\Repositories\UserSaveRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Tests\Unit\Sesame\User\Domain\Entities\UserMother;
use Tests\Utils\Mother\MotherCreator;

final class DeleteUserHandlerTest extends TestCase
{
    private UserSaveRepository|MockObject $userSaveRepository;
    private UserFindRepository|MockObject $userFindRepository;

    protected function setUp(): void
    {
        $this->userSaveRepository = $this->createMock(UserSaveRepository::class);
        $this->userFindRepository = $this->createMock(UserFindRepository::class);
    }

    #[Test]
    public function itShouldDeleteUser(): void
    {
        // GIVEN
        $userExpected = UserMother::random();
        $command      = new DeleteUserCommand($userExpected->id()->toString());

        // WHEN

        $this->userFindRepository
            ->expects(self::once())
            ->method('findById')
            ->with($userExpected->id())
            ->willReturn($userExpected);

        $this->userSaveRepository
            ->expects(self::once())
            ->method('save')
            ->with(
                self::callback(
                    fn (User $user) => $user->id()->equals($userExpected->id())
                        && false === is_null($user->deletedAt())
                )
            );

        $handler = new DeleteUserHandler(
            $this->userSaveRepository,
            $this->userFindRepository,
        );

        // THEN

        $handler($command);
    }

    #[Test]
    public function itShouldThrowAnExceptionWhenNotFoundUser(): void
    {
        // GIVEN

        $userId  = MotherCreator::id();
        $command = new DeleteUserCommand($userId);

        // WHEN

        $this->userFindRepository
            ->expects(self::once())
            ->method('findById')
            ->with(Uuid::fromString($userId))
            ->willReturn(null);

        $this->userSaveRepository
            ->expects(self::never())
            ->method('save');

        $handler = new DeleteUserHandler(
            $this->userSaveRepository,
            $this->userFindRepository,
        );

        // THEN

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage(sprintf('User with id %s not found', $userId));
        $handler($command);
    }
}
