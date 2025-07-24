<?php

declare(strict_types=1);

namespace Tests\Functional\Sesame\WorkEntry\Infrastructure\Api\UpdateWorkEntry;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\Functional\BaseApiTestCase;
use Tests\Unit\Sesame\User\Domain\Entities\UserMother;
use Tests\Unit\Sesame\WorkEntry\Domain\Entities\WorkEntryMother;
use Tests\Utils\Factory\User\UserFactory;
use Tests\Utils\Factory\WorkEntry\WorkEntryFactory;
use Tests\Utils\Mother\MotherCreator;

final class UpdateWorkEntryControllerTest extends BaseApiTestCase
{
    private UserFactory $userFactory;
    private WorkEntryFactory $workEntryFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userFactory      = new UserFactory($this->factoryPersistence());
        $this->workEntryFactory = new WorkEntryFactory($this->factoryPersistence());
    }

    #[Test]
    public function itShouldUpdateWorkEntry(): void
    {
        // GIVEN
        $userId      = MotherCreator::id();
        $userCreated = UserMother::random(['id' => $userId]);
        $this->userFactory->createOne($userCreated);

        $workEntryId      = MotherCreator::id();
        $workEntryCreated = WorkEntryMother::random([
            'id'     => $workEntryId,
            'userId' => $userId,
        ]);
        $this->workEntryFactory->createOne($workEntryCreated);

        $payload = [
            'userId'    => $userId,
            'startDate' => MotherCreator::dateTimeFormat(),
            'endDate'   => MotherCreator::dateTimeFormat(),
            'createdAt' => MotherCreator::dateTimeFormat(),
            'updatedAt' => MotherCreator::dateTimeFormat(),
        ];

        // WHEN
        $this->client()
            ->request(
                'PUT',
                'api/work-entries/' . $workEntryId,
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
    public function itShouldReturnAnStatusCodeNotFoundWhenWorkEntryNotFound(): void
    {
        // GIVEN
        $userId      = MotherCreator::id();
        $userCreated = UserMother::random(['id' => $userId]);
        $this->userFactory->createOne($userCreated);

        $workEntryId = MotherCreator::id();
        $payload     = [
            'userId'    => $userId,
            'startDate' => MotherCreator::dateTimeFormat(),
            'endDate'   => MotherCreator::dateTimeFormat(),
            'createdAt' => MotherCreator::dateTimeFormat(),
            'updatedAt' => MotherCreator::dateTimeFormat(),
        ];

        // WHEN
        $this->client()
            ->request(
                'PUT',
                'api/work-entries/' . $workEntryId,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                json_encode($payload)
            );

        $this->client()->getResponse();

        // THEN
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    #[Test]
    public function itShouldReturnAnStatusCodeNotFoundWhenUserNotFound(): void
    {
        // GIVEN
        $userId      = MotherCreator::id();
        $workEntryId = MotherCreator::id();
        $payload     = [
            'userId'    => $userId,
            'startDate' => MotherCreator::dateTimeFormat(),
            'endDate'   => MotherCreator::dateTimeFormat(),
            'createdAt' => MotherCreator::dateTimeFormat(),
            'updatedAt' => MotherCreator::dateTimeFormat(),
        ];

        // WHEN
        $this->client()
            ->request(
                'PUT',
                'api/work-entries/' . $workEntryId,
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
