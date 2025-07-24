<?php

declare(strict_types=1);

namespace Tests\Unit\Sesame\User\Domain\Services;

use App\Sesame\User\Domain\Entities\User;
use App\Sesame\User\Domain\Exceptions\UserNotFoundException;
use App\Sesame\User\Domain\Repositories\UserFindRepository;
use App\Sesame\User\Domain\Services\EnsureExistsUserByIdService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Tests\Unit\Sesame\User\Domain\Entities\UserMother;
use Tests\Utils\Mother\MotherCreator;

final class EnsureExistsUserByIdServiceTest extends TestCase
{
    private EnsureExistsUserByIdService $service;
    private UserFindRepository|MockObject $userRepository;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserFindRepository::class);
        $this->service        = new EnsureExistsUserByIdService($this->userRepository);
    }

    #[Test]
    public function itShouldFindUser(): void
    {
        // GIVEN

        $userId           = MotherCreator::id();
        $userFindExpected = UserMother::random(['id' => $userId]);

        // WHEN

        $this->userRepository
            ->expects(self::once())
            ->method('findById')
            ->with($userId)
            ->willReturn($userFindExpected);

        $result = ($this->service)(Uuid::fromString($userId));

        // THEN

        self::assertInstanceOf(User::class, $result);
        self::assertEquals($userFindExpected, $result);
    }

    #[Test]
    public function itShouldThrownAnUserNotFoundExceptionWhenNotFoundUser(): void
    {
        // GIVEN
        $userId = Uuid::fromString(MotherCreator::id());

        // WHEN

        $this->userRepository
            ->expects(self::once())
            ->method('findById')
            ->with($userId)
            ->willReturn(null);

        // THEN

        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage('User with id ' . $userId . ' not found');

        ($this->service)($userId);
    }
}
