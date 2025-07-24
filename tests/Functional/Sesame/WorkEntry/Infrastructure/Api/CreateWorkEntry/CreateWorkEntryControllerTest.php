<?php

declare(strict_types=1);

namespace Tests\Functional\Sesame\WorkEntry\Infrastructure\Api\CreateWorkEntry;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\Functional\BaseApiTestCase;
use Tests\Unit\Sesame\User\Domain\Entities\UserMother;
use Tests\Utils\Factory\User\UserFactory;
use Tests\Utils\Mother\MotherCreator;

final class CreateWorkEntryControllerTest extends BaseApiTestCase
{
    private UserFactory $userFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userFactory = new UserFactory($this->factoryPersistence());
    }

    #[Test]
    public function itShouldCreateWorkEntry(): void
    {
        // GIVEN
        $userId      = MotherCreator::id();
        $userCreated = UserMother::random(['id' => $userId]);
        $this->userFactory->createOne($userCreated);
        $payload = [
            'id'        => MotherCreator::id(),
            'userId'    => $userId,
            'startDate' => MotherCreator::dateTimeFormat(),
            'createdAt' => MotherCreator::dateTimeFormat(),
        ];

        // WHEN
        $this->client()
            ->request(
                'POST',
                'api/work-entries',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                json_encode($payload)
            );

        $this->client()->getResponse();

        // THEN
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);
    }
}
