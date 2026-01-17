<?php

declare(strict_types=1);

namespace App\Template\WorkEntry\Domain\Repositories;

use App\Template\WorkEntry\Domain\Collections\WorkEntries;
use App\Template\WorkEntry\Domain\Entities\WorkEntry;
use App\Shared\Domain\Criteria\Criteria;
use Ramsey\Uuid\UuidInterface;

interface WorkEntryFindRepository
{
    public function findById(UuidInterface $id): ?WorkEntry;

    public function searchAllByCriteria(Criteria $criteria): WorkEntries;
}
