<?php

declare(strict_types=1);

namespace Tests\Unit\Sesame\TimeTracking\Application\Commands\ClockOut;

use App\Sesame\TimeTracking\Application\Commands\ClockOut\ClockOutCommand;
use App\Sesame\TimeTracking\Application\Commands\ClockOut\ClockOutHandler;
use App\Sesame\TimeTracking\Domain\Exceptions\WorkEntryAlreadyClockedOutException;
use App\Sesame\TimeTracking\Domain\Exceptions\WorkEntryNotClockedInException;
use App\Sesame\WorkEntry\Domain\Entities\WorkEntry;
use App\Sesame\WorkEntry\Domain\Repositories\WorkEntrySaveRepository;
use App\Sesame\WorkEntry\Domain\Services\EnsureExistWorkEntryByIdService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Tests\Unit\Sesame\WorkEntry\Domain\Entities\WorkEntryMother;
use Tests\Utils\Mother\MotherCreator;

final class ClockOutHandlerTest extends TestCase
{
    private WorkEntrySaveRepository|MockObject $workEntrySaveRepository;
    private EnsureExistWorkEntryByIdService|MockObject $ensureExistsWorkEntryByIdService;
    private ClockOutHandler $handler;

    protected function setUp(): void
    {
        $this->workEntrySaveRepository          = $this->createMock(WorkEntrySaveRepository::class);
        $this->ensureExistsWorkEntryByIdService = $this->createMock(EnsureExistWorkEntryByIdService::class);

        $this->handler = new ClockOutHandler(
            $this->workEntrySaveRepository,
            $this->ensureExistsWorkEntryByIdService,
        );
    }

    #[Test]
    public function itShouldClockOut(): void
    {
        // GIVEN
        $workEntryId = Uuid::fromString(MotherCreator::id());
        $userId      = Uuid::fromString(MotherCreator::id());
        $command     = new ClockOutCommand(
            workEntryId: $workEntryId->toString(),
            userId: $userId->toString(),
            endDate: new \DateTimeImmutable(),
        );

        $workEntryFind = WorkEntryMother::create([
            'id'        => $workEntryId->toString(),
            'userId'    => $userId->toString(),
            'startDate' => new \DateTimeImmutable(),
        ]);

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
                    fn (WorkEntry $workEntry) => $workEntry->isClockedOut()
                )
            );

        // THEN
        ($this->handler)($command);
    }

    #[Test]
    public function itShouldThrowAnExceptionWhenWorkEntryClockOutPreviously(): void
    {
        // GIVEN
        $workEntryId = Uuid::fromString(MotherCreator::id());
        $userId      = Uuid::fromString(MotherCreator::id());
        $command     = new ClockOutCommand(
            workEntryId: $workEntryId->toString(),
            userId: $userId->toString(),
            endDate: new \DateTimeImmutable(),
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

    #[Test]
    public function itShouldThrowAnExceptionWhenWorkEntryNotClockedIn(): void
    {
        // GIVEN
        $workEntryId = Uuid::fromString(MotherCreator::id());
        $userId      = Uuid::fromString(MotherCreator::id());
        $command     = new ClockOutCommand(
            workEntryId: $workEntryId->toString(),
            userId: $userId->toString(),
            endDate: new \DateTimeImmutable(),
        );

        $workEntryFind = WorkEntryMother::create(
            [
                'id'     => $workEntryId->toString(),
                'userId' => $userId->toString(),
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
        $this->expectException(WorkEntryNotClockedInException::class);
        $this->expectExceptionMessage(sprintf('Work entry with id %s is not clocked in', $workEntryId));

        ($this->handler)($command);
    }
}
