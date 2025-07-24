<?php

declare(strict_types=1);

namespace Tests\Functional\Sesame\TimeTracking\Infrastructure\Api\ClockOut;

use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\HttpFoundation\Response;
use Tests\Functional\BaseApiTestCase;
use Tests\Unit\Sesame\WorkEntry\Domain\Entities\WorkEntryMother;
use Tests\Utils\Factory\User\UserFactory;
use Tests\Utils\Factory\WorkEntry\WorkEntryFactory;
use Tests\Utils\Mother\MotherCreator;

final class ClockOutControllerTest extends BaseApiTestCase
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
    public function itShouldClockOutWorkEntry(): void
    {
        // GIVEN
        $user = $this->getUserLogged();

        $workEntryId = MotherCreator::id();
        $startDate   = MotherCreator::dateTime();
        $workEntry   = WorkEntryMother::create([
            'id'     => $workEntryId,
            'userId' => $user->id()->toString(),
        ]);

        // Clock in the work entry
        $workEntry->clockIn($startDate);
        $this->workEntryFactory->createOne($workEntry);

        $payload = [
            'endDate' => MotherCreator::dateTimeFormat(),
        ];

        // WHEN
        $this->client()
            ->request(
                'PATCH',
                'api/work-entries/' . $workEntryId . '/clock-out',
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
}
