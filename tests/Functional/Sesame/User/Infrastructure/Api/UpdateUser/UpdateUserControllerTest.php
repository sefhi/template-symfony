<?php

declare(strict_types=1);

namespace Tests\Functional\Sesame\User\Infrastructure\Api\UpdateUser;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\Functional\BaseApiTestCase;
use Tests\Unit\Sesame\User\Domain\Entities\UserMother;
use Tests\Utils\Factory\User\UserFactory;
use Tests\Utils\Mother\MotherCreator;

final class UpdateUserControllerTest extends BaseApiTestCase
{
    private UserFactory $userFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userFactory = new UserFactory($this->factoryPersistence());
    }

    #[Test]
    public function itShouldUpdateUser(): void
    {
        // GIVEN
        $userCreated = UserMother::random();
        $this->userFactory->createOne($userCreated);

        $userId = $userCreated->id();

        $payload = [
            'name'      => MotherCreator::name(),
            'email'     => MotherCreator::email(),
            'createdAt' => new \DateTimeImmutable()->format(\DateTimeInterface::ATOM),
            'updatedAt' => new \DateTimeImmutable()->format(\DateTimeInterface::ATOM),
        ];

        // WHEN
        $this->client()
            ->request(
                'PUT',
                'api/users/' . $userId,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                json_encode($payload)
            );

        $this->client()->getResponse();

        // THEN
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    #[Test]
    public function itShouldReturnAnStatusCodeNotFoundWhenUserNotFound(): void
    {
        // GIVEN
        $userId  = MotherCreator::id();
        $payload = [
            'name'      => MotherCreator::name(),
            'email'     => MotherCreator::email(),
            'createdAt' => new \DateTimeImmutable()->format(\DateTimeInterface::ATOM),
            'updatedAt' => new \DateTimeImmutable()->format(\DateTimeInterface::ATOM),
        ];

        // WHEN
        $this->client()
            ->request(
                'PUT',
                'api/users/' . $userId,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                json_encode($payload)
            );

        $this->client()->getResponse();

        // THEN
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
