<?php

declare(strict_types=1);

namespace Tests\Unit\Sesame\WorkEntry\Application\Commands\UpdateWorkEntry;

use App\Sesame\User\Domain\Services\EnsureExistsUserByIdService;
use App\Sesame\WorkEntry\Application\Commands\UpdateWorkEntry\UpdateWorkEntryHandler;
use App\Sesame\WorkEntry\Domain\Repositories\WorkEntryFindRepository;
use App\Sesame\WorkEntry\Domain\Repositories\WorkEntrySaveRepository;
use App\Sesame\WorkEntry\Domain\Services\EnsureExistWorkEntryByIdService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Tests\Unit\Sesame\User\Domain\Entities\UserMother;
use Tests\Unit\Sesame\WorkEntry\Domain\Entities\WorkEntryMother;
use Tests\Utils\Mother\MotherCreator;

final class UpdateWorkEntryHandlerTest extends TestCase
{
    private WorkEntrySaveRepository|MockObject $workEntrySaveRepository;
    private WorkEntryFindRepository|MockObject $workEntryFindRepository;
    private EnsureExistsUserByIdService|MockObject $ensureExistsUserByIdService;
    private EnsureExistWorkEntryByIdService|MockObject $ensureExistsWorkEntryByIdService;
    private UpdateWorkEntryHandler $handler;

    protected function setUp(): void
    {
        $this->workEntrySaveRepository          = $this->createMock(WorkEntrySaveRepository::class);
        $this->ensureExistsWorkEntryByIdService = $this->createMock(EnsureExistWorkEntryByIdService::class);
        $this->ensureExistsUserByIdService      = $this->createMock(EnsureExistsUserByIdService::class);

        $this->handler = new UpdateWorkEntryHandler(
            $this->workEntrySaveRepository,
            $this->ensureExistsWorkEntryByIdService,
            $this->ensureExistsUserByIdService,
        );
    }

    #[Test]
    public function itShouldUpdateWorkEntry(): void
    {
        // GIVEN

        $userId  = Uuid::fromString(MotherCreator::id());
        $command = UpdateWorkEntryCommandMother::random(['userId' => $userId->toString()]);

        $workEntryFind = WorkEntryMother::random(
            ['id' => $command->id, 'userId' => $command->userId],
        );
        $workEntryExpected = WorkEntryMother::fromUpdateWorkEntryCommand($command);
        $userExpected      = UserMother::random(['id' => $userId->toString()]);

        // WHEN

        $this->ensureExistsUserByIdService
            ->expects(self::once())
            ->method('__invoke')
            ->with($userId)
            ->willReturn($userExpected);

        $this->ensureExistsWorkEntryByIdService
            ->expects(self::once())
            ->method('__invoke')
            ->with(Uuid::fromString($command->id))
            ->willReturn($workEntryFind);

        $this->workEntrySaveRepository
            ->expects(self::once())
            ->method('save')
            ->with($workEntryExpected);

        // THEN

        ($this->handler)($command);
    }
}
