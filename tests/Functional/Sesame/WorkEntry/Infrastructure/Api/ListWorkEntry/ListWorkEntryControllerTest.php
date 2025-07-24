<?php

declare(strict_types=1);

namespace Tests\Functional\Sesame\WorkEntry\Infrastructure\Api\ListWorkEntry;

use App\Sesame\WorkEntry\Application\Queries\ListWorkEntry\ListWorkEntryResponse;
use App\Sesame\WorkEntry\Domain\Collections\WorkEntries;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\Functional\BaseApiTestCase;
use Tests\Unit\Sesame\WorkEntry\Domain\Entities\WorkEntryMother;
use Tests\Utils\Factory\WorkEntry\WorkEntryFactory;

final class ListWorkEntryControllerTest extends BaseApiTestCase
{
    private WorkEntryFactory $workEntryFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->workEntryFactory = new WorkEntryFactory($this->factoryPersistence());
    }

    #[Test]
    public function itShouldListWorkEntriesForUser(): void
    {
        // GIVEN
        $user   = $this->getUserLogged();
        $userId = $user->id()->toString();

        $workEntry1 = WorkEntryMother::random(
            [
                'userId'    => $userId,
                'createdAt' => new \DateTimeImmutable('2022-01-01 00:00:00'),
            ]
        );
        $workEntry2 = WorkEntryMother::random(
            [
                'userId'    => $userId,
                'createdAt' => new \DateTimeImmutable('2022-01-02 00:00:00'),
            ]
        );
        $this->workEntryFactory->createOne($workEntry1);
        $this->workEntryFactory->createOne($workEntry2);

        $workEntries                 = new WorkEntries([$workEntry2, $workEntry1]);
        $workEntriesResponseExpected = ListWorkEntryResponse::fromWorkEntries($workEntries);
        $contentExpected             = json_decode(json_encode($workEntriesResponseExpected->jsonSerialize()), true);

        // WHEN
        $this->client()
            ->request(
                'GET',
                'api/work-entries', // TODO #query params??
                [],
                [],
                ['CONTENT_TYPE' => 'application/json']
            );

        $response = $this->client()->getResponse();
        $content  = json_decode($response->getContent(), true, JSON_THROW_ON_ERROR, 512);

        // THEN
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertEquals($contentExpected, $content);
    }

    #[Test]
    public function itShouldReturnEmptyArrayWhenNoWorkEntriesForUser(): void
    {
        // GIVEN

        // WHEN
        $this->client()
            ->request(
                'GET',
                'api/work-entries',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json']
            );

        $response = $this->client()->getResponse();
        $content  = json_decode($response->getContent(), true, JSON_THROW_ON_ERROR, 512);

        // THEN
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertEquals([], $content);
    }

    #[Test]
    public function itShouldNotIncludeDeletedWorkEntries(): void
    {
        // GIVEN
        $user   = $this->getUserLogged();
        $userId = $user->id()->toString();

        $workEntry1 = WorkEntryMother::random(['userId' => $userId]);
        $workEntry2 = WorkEntryMother::random([
            'userId'    => $userId,
            'deletedAt' => new \DateTimeImmutable(),
        ]);
        $this->workEntryFactory->createOne($workEntry1);
        $this->workEntryFactory->createOne($workEntry2);

        $workEntries                 = new WorkEntries([$workEntry1]);
        $workEntriesResponseExpected = ListWorkEntryResponse::fromWorkEntries($workEntries);
        $contentExpected             = json_decode(json_encode($workEntriesResponseExpected->jsonSerialize()), true);

        // WHEN
        $this->client()
            ->request(
                'GET',
                'api/work-entries',
                [],
                [],
                ['CONTENT_TYPE' => 'application/json']
            );

        $response = $this->client()->getResponse();
        $content  = json_decode($response->getContent(), true, JSON_THROW_ON_ERROR, 512);

        // THEN
        self::assertResponseIsSuccessful();
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertEquals($contentExpected, $content);
    }
}
