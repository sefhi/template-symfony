<?php

declare(strict_types=1);

namespace Tests\Unit\Sesame\WorkEntry\Application\Commands\DeleteWorkEntry;

use App\Sesame\WorkEntry\Application\Commands\DeleteWorkEntry\DeleteWorkEntryCommand;
use App\Sesame\WorkEntry\Application\Commands\DeleteWorkEntry\DeleteWorkEntryHandler;
use App\Sesame\WorkEntry\Domain\Entities\WorkEntry;
use App\Sesame\WorkEntry\Domain\Repositories\WorkEntrySaveRepository;
use App\Sesame\WorkEntry\Domain\Services\EnsureExistWorkEntryByIdService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Tests\Unit\Sesame\WorkEntry\Domain\Entities\WorkEntryMother;
use Tests\Utils\Mother\MotherCreator;

final class DeleteWorkEntryHandlerTest extends TestCase
{
    private WorkEntrySaveRepository|MockObject $workEntrySaveRepository;
    private EnsureExistWorkEntryByIdService|MockObject $ensureExistWorkEntryByIdService;
    private DeleteWorkEntryHandler $handler;

    protected function setUp(): void
    {
        $this->workEntrySaveRepository         = $this->createMock(WorkEntrySaveRepository::class);
        $this->ensureExistWorkEntryByIdService = $this->createMock(EnsureExistWorkEntryByIdService::class);

        $this->handler = new DeleteWorkEntryHandler(
            $this->workEntrySaveRepository,
            $this->ensureExistWorkEntryByIdService
        );
    }

    #[Test]
    public function itShouldDeleteWorkEntry(): void
    {
        // GIVEN

        $workEntryId = Uuid::fromString(MotherCreator::id());
        $command     = new DeleteWorkEntryCommand(
            $workEntryId->toString(),
        );
        $workEntryFind = WorkEntryMother::random(['id' => $workEntryId->toString()]);

        // WHEN

        $this->ensureExistWorkEntryByIdService
            ->expects(self::once())
            ->method('__invoke')
            ->with($workEntryId)
            ->willReturn($workEntryFind);

        $this->workEntrySaveRepository
            ->expects(self::once())
            ->method('save')
            ->with(
                self::callback(
                    fn (WorkEntry $workEntry) => $workEntry->isDeleted()
                )
            );

        // THEN

        ($this->handler)($command);
    }
}
