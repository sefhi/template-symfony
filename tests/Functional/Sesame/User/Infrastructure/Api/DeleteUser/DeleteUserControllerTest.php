<?php

declare(strict_types=1);

namespace Tests\Functional\Sesame\User\Infrastructure\Api\DeleteUser;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\Functional\BaseApiTestCase;
use Tests\Unit\Sesame\User\Domain\Entities\UserMother;
use Tests\Utils\Factory\User\UserFactory;
use Tests\Utils\Mother\MotherCreator;

final class DeleteUserControllerTest extends BaseApiTestCase
{
    private UserFactory $userFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userFactory = new UserFactory($this->factoryPersistence());
    }

    #[Test]
    public function itShouldDeleteUser(): void
    {
        // GIVEN
        $userCreated = UserMother::random();
        $this->userFactory->createOne($userCreated);

        $userId = $userCreated->id();

        // WHEN
        $this->client()
            ->request(
                'DELETE',
                'api/users/' . $userId,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json']
            );

        $this->client()->getResponse();

        // THEN
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    #[Test]
    public function itShouldReturnAnStatusCodeNotFoundWhenUserNotFound(): void
    {
        // GIVEN
        $userId = MotherCreator::id();

        // WHEN
        $this->client()
            ->request(
                'DELETE',
                'api/users/' . $userId,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json']
            );

        $this->client()->getResponse();

        // THEN
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
