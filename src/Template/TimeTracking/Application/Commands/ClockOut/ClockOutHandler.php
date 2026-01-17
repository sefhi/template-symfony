<?php

declare(strict_types=1);

namespace App\Template\TimeTracking\Application\Commands\ClockOut;

use App\Template\TimeTracking\Domain\Exceptions\WorkEntryAlreadyClockedOutException;
use App\Template\TimeTracking\Domain\Exceptions\WorkEntryNotBelongToUserException;
use App\Template\TimeTracking\Domain\Exceptions\WorkEntryNotClockedInException;
use App\Template\WorkEntry\Domain\Repositories\WorkEntrySaveRepository;
use App\Template\WorkEntry\Domain\Services\EnsureExistWorkEntryByIdService;
use App\Shared\Domain\Bus\Command\CommandHandler;
use Ramsey\Uuid\Uuid;

final readonly class ClockOutHandler implements CommandHandler
{
    public function __construct(
        private WorkEntrySaveRepository $workEntrySaveRepository,
        private EnsureExistWorkEntryByIdService $ensureExistsWorkEntryByIdService,
    ) {
    }

    public function __invoke(ClockOutCommand $command): void
    {
        $userId      = Uuid::fromString($command->userId);
        $workEntryId = Uuid::fromString($command->workEntryId);

        $workEntry = ($this->ensureExistsWorkEntryByIdService)($workEntryId);

        if (false === $workEntry->userId()->equals($userId)) {
            throw WorkEntryNotBelongToUserException::withId($workEntryId);
        }

        if ($workEntry->isClockedOut()) {
            throw WorkEntryAlreadyClockedOutException::withId($workEntryId);
        }

        if (!$workEntry->isClockedIn()) {
            throw WorkEntryNotClockedInException::withId($workEntryId);
        }

        $workEntry->clockOut($command->endDate);

        $this->workEntrySaveRepository->save($workEntry);
    }
}
