<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Domain\Repositories;

use App\Sesame\WorkEntry\Domain\Collections\WorkEntries;
use App\Sesame\WorkEntry\Domain\Entities\WorkEntry;
use App\Shared\Domain\Criteria\Criteria;
use Ramsey\Uuid\UuidInterface;

interface WorkEntryFindRepository
{
    public function findById(UuidInterface $id): ?WorkEntry;

    public function searchAllByCriteria(Criteria $criteria): WorkEntries;
}
