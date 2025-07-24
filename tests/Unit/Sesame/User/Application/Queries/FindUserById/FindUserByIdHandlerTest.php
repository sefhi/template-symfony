<?php

declare(strict_types=1);

namespace Tests\Unit\Sesame\User\Application\Queries\FindUserById;

use App\Sesame\User\Application\Queries\FindUserById\FindUserByIdHandler;
use App\Sesame\User\Application\Queries\FindUserById\FindUserByIdQuery;
use App\Sesame\User\Application\Queries\FindUserById\UserResponse;
use App\Sesame\User\Domain\Services\EnsureExistsUserByIdService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\Unit\Sesame\User\Domain\Entities\UserMother;
use Tests\Utils\Mother\MotherCreator;

final class FindUserByIdHandlerTest extends TestCase
{
    private EnsureExistsUserByIdService|MockObject $service;
    private FindUserByIdHandler $handler;

    protected function setUp(): void
    {
        $this->service = $this->createMock(EnsureExistsUserByIdService::class);
        $this->handler = new FindUserByIdHandler($this->service);
    }

    #[Test]
    public function itShouldFindUserById(): void
    {
        // GIVEN

        $userId               = MotherCreator::id();
        $userFind             = UserMother::random(['id' => $userId]);
        $userResponseExpected = UserResponse::fromUser($userFind);
        $query                = new FindUserByIdQuery($userId);

        // WHEN

        $this->service
            ->expects(self::once())
            ->method('__invoke')
            ->with($userId)
            ->willReturn($userFind);

        $result = ($this->handler)($query);

        // THEN

        self::assertInstanceOf(UserResponse::class, $result);
        self::assertEquals($userResponseExpected, $result);
    }
}
