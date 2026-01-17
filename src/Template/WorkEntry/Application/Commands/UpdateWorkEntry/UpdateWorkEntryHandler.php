<?php

declare(strict_types=1);

namespace App\Template\WorkEntry\Application\Commands\UpdateWorkEntry;

use App\Template\User\Domain\Services\EnsureExistsUserByIdService;
use App\Template\WorkEntry\Domain\Repositories\WorkEntrySaveRepository;
use App\Template\WorkEntry\Domain\Services\EnsureExistWorkEntryByIdService;
use App\Shared\Domain\Bus\Command\CommandHandler;
use Ramsey\Uuid\Uuid;

final readonly class UpdateWorkEntryHandler implements CommandHandler
{
    public function __construct(
        private WorkEntrySaveRepository $workEntrySaveRepository,
        private EnsureExistWorkEntryByIdService $ensureExistWorkEntryByIdService,
        private EnsureExistsUserByIdService $ensureExistsUserByIdService,
    ) {
    }

    public function __invoke(UpdateWorkEntryCommand $command): void
    {
        $userId      = Uuid::fromString($command->userId);
        $workEntryId = Uuid::fromString($command->id);

        ($this->ensureExistsUserByIdService)($userId);

        $workEntry = ($this->ensureExistWorkEntryByIdService)($workEntryId);

        $workEntry->update(
            $command->userId,
            $command->startDate,
            $command->createdAt,
            $command->updatedAt,
            $command->endDate,
            $command->deletedAt,
        );

        $this->workEntrySaveRepository->save($workEntry);
    }
}
