<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Application\Commands\CreateWorkEntry;

use App\Sesame\User\Domain\Services\EnsureExistsUserByIdService;
use App\Sesame\WorkEntry\Domain\Entities\WorkEntry;
use App\Sesame\WorkEntry\Domain\Repositories\WorkEntrySaveRepository;
use App\Shared\Domain\Bus\Command\CommandHandler;
use Ramsey\Uuid\Uuid;

final readonly class CreateWorkEntryHandler implements CommandHandler
{
    public function __construct(
        private WorkEntrySaveRepository $workEntryRepository,
        private EnsureExistsUserByIdService $ensureExistsUserByIdService,
    ) {
    }

    public function __invoke(CreateWorkEntryCommand $command): void
    {
        $userId = Uuid::fromString($command->userId);

        ($this->ensureExistsUserByIdService)($userId);

        $workEntry = WorkEntry::create(
            id: $command->id,
            userId: $command->userId,
            startDate: $command->startDate,
            createdAt: $command->createdAt,
        );

        $this->workEntryRepository->save($workEntry);
    }
}
