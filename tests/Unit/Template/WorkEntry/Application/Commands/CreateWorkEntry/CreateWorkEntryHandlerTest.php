<?php

declare(strict_types=1);

namespace Tests\Unit\Template\WorkEntry\Application\Commands\CreateWorkEntry;

use App\Template\User\Domain\Services\EnsureExistsUserByIdService;
use App\Template\WorkEntry\Application\Commands\CreateWorkEntry\CreateWorkEntryHandler;
use App\Template\WorkEntry\Domain\Repositories\WorkEntrySaveRepository;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Tests\Unit\Template\User\Domain\Entities\UserMother;
use Tests\Unit\Template\WorkEntry\Domain\Entities\WorkEntryMother;

final class CreateWorkEntryHandlerTest extends TestCase
{
    private WorkEntrySaveRepository|MockObject $workEntryRepository;
    private EnsureExistsUserByIdService|MockObject $ensureExistsUserByIdService;
    private CreateWorkEntryHandler $handler;

    protected function setUp(): void
    {
        $this->workEntryRepository         = $this->createMock(WorkEntrySaveRepository::class);
        $this->ensureExistsUserByIdService = $this->createMock(EnsureExistsUserByIdService::class);
        $this->handler                     = new CreateWorkEntryHandler(
            $this->workEntryRepository,
            $this->ensureExistsUserByIdService,
        );
    }

    #[Test]
    public function itShouldCreateWorkEntry(): void
    {
        // GIVEN
        $command           = CreateWorkEntryCommandMother::random();
        $userId            = Uuid::fromString($command->userId);
        $userExpected      = UserMother::random(['id' => $command->userId]);
        $workEntryExpected = WorkEntryMother::fromCreateWorkEntryCommand($command);

        // WHEN

        $this->ensureExistsUserByIdService
            ->expects(self::once())
            ->method('__invoke')
            ->with($userId)
            ->willReturn($userExpected);

        $this->workEntryRepository
            ->expects(self::once())
            ->method('save')
            ->with($workEntryExpected);

        // THEN

        ($this->handler)($command);
    }
}
