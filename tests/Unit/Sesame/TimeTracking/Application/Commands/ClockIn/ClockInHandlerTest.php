<?php

declare(strict_types=1);

namespace Tests\Unit\Sesame\TimeTracking\Application\Commands\ClockIn;

use App\Sesame\TimeTracking\Application\Commands\ClockIn\ClockInCommand;
use App\Sesame\TimeTracking\Application\Commands\ClockIn\ClockInHandler;
use App\Sesame\TimeTracking\Domain\Exceptions\WorkEntryAlreadyClockedInException;
use App\Sesame\TimeTracking\Domain\Exceptions\WorkEntryAlreadyClockedOutException;
use App\Sesame\WorkEntry\Domain\Entities\WorkEntry;
use App\Sesame\WorkEntry\Domain\Repositories\WorkEntrySaveRepository;
use App\Sesame\WorkEntry\Domain\Services\EnsureExistWorkEntryByIdService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Tests\Unit\Sesame\WorkEntry\Domain\Entities\WorkEntryMother;
use Tests\Utils\Mother\MotherCreator;

final class ClockInHandlerTest extends TestCase
{
    private WorkEntrySaveRepository|MockObject $workEntrySaveRepository;
    private EnsureExistWorkEntryByIdService|MockObject $ensureExistsWorkEntryByIdService;
    private ClockInHandler $handler;

    protected function setUp(): void
    {
        $this->workEntrySaveRepository          = $this->createMock(WorkEntrySaveRepository::class);
        $this->ensureExistsWorkEntryByIdService = $this->createMock(EnsureExistWorkEntryByIdService::class);

        $this->handler = new ClockInHandler(
            $this->workEntrySaveRepository,
            $this->ensureExistsWorkEntryByIdService,
        );
    }

    #[Test]
    public function itShouldClockIn(): void
    {
        // GIVEN

        $workEntryId = Uuid::fromString(MotherCreator::id());
        $userId      = Uuid::fromString(MotherCreator::id());
        $command     = new ClockInCommand(
            workEntryId: $workEntryId->toString(),
            userId: $userId->toString(),
            startDate: new \DateTimeImmutable(),
        );

        $workEntryFind = WorkEntryMother::create(['id' => $workEntryId->toString(), 'userId' => $userId->toString()]);

        // WHEN

        $this->ensureExistsWorkEntryByIdService
            ->expects(self::once())
            ->method('__invoke')
            ->with($workEntryId)
            ->willReturn($workEntryFind);

        $this->workEntrySaveRepository
            ->expects(self::once())
            ->method('save')
            ->with(
                self::callback(
                    fn (WorkEntry $workEntry) => $workEntry->isClockedIn()
                )
            );

        // THEN

        ($this->handler)($command);
    }

    #[Test]
    public function itShouldThrowAnExceptionWhenWorkEntryClockInPreviously(): void
    {
        // GIVEN

        $workEntryId = Uuid::fromString(MotherCreator::id());
        $userId      = Uuid::fromString(MotherCreator::id());
        $command     = new ClockInCommand(
            workEntryId: $workEntryId->toString(),
            userId: $userId->toString(),
            startDate: new \DateTimeImmutable(),
        );

        $workEntryFind = WorkEntryMother::create(
            [
                'id'        => $workEntryId->toString(),
                'userId'    => $userId->toString(),
                'startDate' => new \DateTimeImmutable(),
            ]
        );

        // WHEN

        $this->ensureExistsWorkEntryByIdService
            ->expects(self::once())
            ->method('__invoke')
            ->with($workEntryId)
            ->willReturn($workEntryFind);

        $this->workEntrySaveRepository
            ->expects(self::never())
            ->method('save');

        // THEN

        $this->expectException(WorkEntryAlreadyClockedInException::class);
        $this->expectExceptionMessage(sprintf('Work entry with id %s already clocked in', $workEntryId));

        ($this->handler)($command);
    }

    #[Test]
    public function itShouldThrowAnExceptionWhenWorkEntryClockOutPreviously(): void
    {
        // GIVEN

        $workEntryId = Uuid::fromString(MotherCreator::id());
        $userId      = Uuid::fromString(MotherCreator::id());
        $command     = new ClockInCommand(
            workEntryId: $workEntryId->toString(),
            userId: $userId->toString(),
            startDate: new \DateTimeImmutable(),
        );

        $workEntryFind = WorkEntryMother::random(
            [
                'id'        => $workEntryId->toString(),
                'userId'    => $userId->toString(),
                'startDate' => new \DateTimeImmutable(),
                'endDate'   => new \DateTimeImmutable(),
            ]
        );

        // WHEN

        $this->ensureExistsWorkEntryByIdService
            ->expects(self::once())
            ->method('__invoke')
            ->with($workEntryId)
            ->willReturn($workEntryFind);

        $this->workEntrySaveRepository
            ->expects(self::never())
            ->method('save');

        // THEN

        $this->expectException(WorkEntryAlreadyClockedOutException::class);
        $this->expectExceptionMessage(sprintf('Work entry with id %s already clocked out', $workEntryId));

        ($this->handler)($command);
    }
}
