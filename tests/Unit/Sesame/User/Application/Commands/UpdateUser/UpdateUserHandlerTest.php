<?php

declare(strict_types=1);

namespace Tests\Unit\Sesame\User\Application\Commands\UpdateUser;

use App\Sesame\User\Application\Commands\UpdateUser\UpdateUserHandler;
use App\Sesame\User\Domain\Entities\User;
use App\Sesame\User\Domain\Exceptions\UserNotFoundException;
use App\Sesame\User\Domain\Repositories\UserFindRepository;
use App\Sesame\User\Domain\Repositories\UserSaveRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Tests\Unit\Sesame\User\Domain\Entities\UserMother;

final class UpdateUserHandlerTest extends TestCase
{
    private UserSaveRepository|MockObject $userSaveRepository;
    private UserFindRepository|MockObject $userFindRepository;

    protected function setUp(): void
    {
        $this->userSaveRepository = $this->createMock(UserSaveRepository::class);
        $this->userFindRepository = $this->createMock(UserFindRepository::class);
    }

    #[Test]
    public function itShouldUpdateUser(): void
    {
        // GIVEN

        $nameExpected  = 'New name';
        $emailExpected = 'new_email@hotmail.es';
        $command       = UpdateUserCommandMother::random(
            [
                'name'  => $nameExpected,
                'email' => $emailExpected,
            ]
        );
        $userExpected = UserMother::random(
            [
                'id'        => $command->id,
                'createdAt' => $command->createdAt,
                'updatedAt' => null,
                'name'      => 'Old name',
                'email'     => 'emailOld@hotmail.es',
            ]
        );

        // WHEN

        $this->userFindRepository
            ->expects(self::once())
            ->method('findById')
            ->with(Uuid::fromString($command->id))
            ->willReturn($userExpected);

        $this->userSaveRepository
            ->expects(self::once())
            ->method('save')
            ->with(
                self::callback(
                    fn (User $user) => $user->id()->equals($userExpected->id())
                        && $user->createdAt() === $userExpected->createdAt()
                        && false === is_null($user->updatedAt())
                        && $user->name()->value() === $nameExpected
                        && $user->email()->value() === $emailExpected
                )
            );

        $handler = new UpdateUserHandler(
            $this->userSaveRepository,
            $this->userFindRepository
        );

        // THEN

        ($handler)($command);
    }

    #[Test]
    public function itShouldThrowAnExceptionWhenNotFoundUser(): void
    {
        // GIVEN

        $command = UpdateUserCommandMother::random();

        // WHEN

        $this->userFindRepository
            ->expects(self::once())
            ->method('findById')
            ->with(Uuid::fromString($command->id))
            ->willReturn(null);

        $this->userSaveRepository
            ->expects(self::never())
            ->method('save');

        $handler = new UpdateUserHandler(
            $this->userSaveRepository,
            $this->userFindRepository
        );

        // THEN

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage(sprintf('User with id %s not found', $command->id));

        ($handler)($command);
    }
}
