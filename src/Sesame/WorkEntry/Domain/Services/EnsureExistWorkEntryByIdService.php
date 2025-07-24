<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Domain\Services;

use App\Sesame\WorkEntry\Domain\Entities\WorkEntry;
use App\Sesame\WorkEntry\Domain\Exceptions\WorkEntryNotFoundException;
use App\Sesame\WorkEntry\Domain\Repositories\WorkEntryFindRepository;
use Ramsey\Uuid\UuidInterface;

class EnsureExistWorkEntryByIdService
{
    public function __construct(
        private readonly WorkEntryFindRepository $workEntryFindRepository,
    ) {
    }

    public function __invoke(UuidInterface $id): WorkEntry
    {
        $workEntry = $this->workEntryFindRepository->findById($id);

        if (null === $workEntry) {
            throw WorkEntryNotFoundException::withId($id);
        }

        return $workEntry;
    }
}
