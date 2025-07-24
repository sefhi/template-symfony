<?php

declare(strict_types=1);

namespace Tests\Functional\Sesame\User\Infrastructure\Api\FindUserById;

use App\Sesame\User\Application\Queries\FindUserById\UserResponse;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\Functional\BaseApiTestCase;
use Tests\Unit\Sesame\User\Domain\Entities\UserMother;
use Tests\Utils\Factory\User\UserFactory;
use Tests\Utils\Mother\MotherCreator;

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
        $userCreated = UserMother::random();
        $this->userFactory->createOne($userCreated);
        $userResponseExpected = UserResponse::fromUser($userCreated);

        $userId = $userCreated->id();

        // WHEN
        $this->client()
            ->request(
                'GET',
                'api/users/' . $userId,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json']
            );

        $response = $this->client()->getResponse();
        $content  = json_decode($response->getContent(), true, JSON_THROW_ON_ERROR, 512);

        // THEN
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertEquals($userResponseExpected->jsonSerialize(), $content);
    }

    #[Test]
    public function itShouldReturnAnStatusCodeNotFoundWhenUserNotFound(): void
    {
        // GIVEN
        $userId = MotherCreator::id();

        // WHEN
        $this->client()
            ->request(
                'GET',
                'api/users/' . $userId,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json']
            );

        $this->client()->getResponse();

        // THEN
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    #[Test]
    public function itNotShouldFindUserByIdWhenUserIsDeleted(): void
    {
        // GIVEN
        $userDeleted = UserMother::random(['deletedAt' => new \DateTimeImmutable('now')]);
        $this->userFactory->createOne($userDeleted);

        $userId = $userDeleted->id();

        // WHEN
        $this->client()
            ->request(
                'GET',
                'api/users/' . $userId,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json']
            );

        $response = $this->client()->getResponse();
        $content  = json_decode($response->getContent(), true, JSON_THROW_ON_ERROR, 512);

        // THEN
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
