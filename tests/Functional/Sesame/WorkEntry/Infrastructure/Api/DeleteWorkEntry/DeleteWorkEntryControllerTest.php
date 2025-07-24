<?php

declare(strict_types=1);

namespace Tests\Functional\Sesame\WorkEntry\Infrastructure\Api\DeleteWorkEntry;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\Functional\BaseApiTestCase;
use Tests\Unit\Sesame\User\Domain\Entities\UserMother;
use Tests\Unit\Sesame\WorkEntry\Domain\Entities\WorkEntryMother;
use Tests\Utils\Factory\User\UserFactory;
use Tests\Utils\Factory\WorkEntry\WorkEntryFactory;
use Tests\Utils\Mother\MotherCreator;

final class DeleteWorkEntryControllerTest extends BaseApiTestCase
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
    public function itShouldDeleteWorkEntry(): void
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

        // WHEN
        $this->client()
            ->request(
                'DELETE',
                'api/work-entries/' . $workEntryId,
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
    public function itShouldReturnAnStatusCodeNotFoundWhenWorkEntryNotFound(): void
    {
        // GIVEN
        $workEntryId = MotherCreator::id();

        // WHEN
        $this->client()
            ->request(
                'DELETE',
                'api/work-entries/' . $workEntryId,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json']
            );

        $this->client()->getResponse();

        // THEN
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }
}
