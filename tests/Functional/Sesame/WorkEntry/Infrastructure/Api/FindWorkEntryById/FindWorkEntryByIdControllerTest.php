<?php

declare(strict_types=1);

namespace Tests\Functional\Sesame\WorkEntry\Infrastructure\Api\FindWorkEntryById;

use App\Sesame\WorkEntry\Application\Queries\WorkEntryResponse;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\Functional\BaseApiTestCase;
use Tests\Unit\Sesame\User\Domain\Entities\UserMother;
use Tests\Unit\Sesame\WorkEntry\Domain\Entities\WorkEntryMother;
use Tests\Utils\Factory\User\UserFactory;
use Tests\Utils\Factory\WorkEntry\WorkEntryFactory;
use Tests\Utils\Mother\MotherCreator;

final class FindWorkEntryByIdControllerTest extends BaseApiTestCase
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
    public function itShouldFindWorkEntryById(): void
    {
        // GIVEN
        $userId      = MotherCreator::id();
        $userCreated = UserMother::random(['id' => $userId]);
        $this->userFactory->createOne($userCreated);

        $workEntryCreated = WorkEntryMother::random(['userId' => $userId]);
        $this->workEntryFactory->createOne($workEntryCreated);
        $workEntryResponseExpected = WorkEntryResponse::fromWorkEntry($workEntryCreated);

        $workEntryId = $workEntryCreated->id();

        // WHEN
        $this->client()
            ->request(
                'GET',
                'api/work-entries/' . $workEntryId,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json']
            );

        $response = $this->client()->getResponse();
        $content  = json_decode($response->getContent(), true, JSON_THROW_ON_ERROR, 512);

        // THEN
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertEquals($workEntryResponseExpected->jsonSerialize(), $content);
    }

    #[Test]
    public function itShouldReturnAnStatusCodeNotFoundWhenWorkEntryNotFound(): void
    {
        // GIVEN
        $workEntryId = MotherCreator::id();

        // WHEN
        $this->client()
            ->request(
                'GET',
                'api/work-entries/' . $workEntryId,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json']
            );

        $this->client()->getResponse();

        // THEN
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    #[Test]
    public function itNotShouldFindWorkEntryByIdWhenWorkEntryIsDeleted(): void
    {
        // GIVEN
        $userId      = MotherCreator::id();
        $userCreated = UserMother::random(['id' => $userId]);
        $this->userFactory->createOne($userCreated);

        $workEntryDeleted = WorkEntryMother::random([
            'userId'    => $userId,
            'deletedAt' => new \DateTimeImmutable(),
        ]);
        $this->workEntryFactory->createOne($workEntryDeleted);

        $workEntryId = $workEntryDeleted->id();

        // WHEN
        $this->client()
            ->request(
                'GET',
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
