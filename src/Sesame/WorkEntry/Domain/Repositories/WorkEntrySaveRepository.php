<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Domain\Repositories;

use App\Sesame\WorkEntry\Domain\Entities\WorkEntry;

interface WorkEntrySaveRepository
{
    public function save(WorkEntry $workEntry): void;
}
