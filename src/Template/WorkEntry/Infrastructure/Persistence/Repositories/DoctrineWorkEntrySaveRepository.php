<?php

declare(strict_types=1);

namespace App\Template\WorkEntry\Infrastructure\Persistence\Repositories;

use App\Template\WorkEntry\Domain\Entities\WorkEntry;
use App\Template\WorkEntry\Domain\Repositories\WorkEntrySaveRepository;
use App\Shared\Infrastructure\Persistence\Repository\DoctrineRepository;

final readonly class DoctrineWorkEntrySaveRepository extends DoctrineRepository implements WorkEntrySaveRepository
{
    public function save(WorkEntry $workEntry): void
    {
        $this->persist($workEntry);
    }
}
