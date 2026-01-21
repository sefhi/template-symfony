<?php

declare(strict_types=1);

namespace App\Template\WorkEntry\Application\Commands\DeleteWorkEntry;

use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Template\WorkEntry\Domain\Repositories\WorkEntrySaveRepository;
use App\Template\WorkEntry\Domain\Services\EnsureExistWorkEntryByIdService;
use Ramsey\Uuid\Uuid;

final readonly class DeleteWorkEntryHandler implements CommandHandler
{
    public function __construct(
        private WorkEntrySaveRepository $workEntryRepository,
        private EnsureExistWorkEntryByIdService $ensureExistWorkEntryByIdService,
    ) {
    }

    public function __invoke(DeleteWorkEntryCommand $command): void
    {
        $workEntryId = Uuid::fromString($command->id);
        $workEntry   = ($this->ensureExistWorkEntryByIdService)($workEntryId);

        $workEntry->delete();

        $this->workEntryRepository->save($workEntry);
    }
}
