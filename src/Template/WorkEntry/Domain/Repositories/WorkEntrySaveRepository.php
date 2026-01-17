<?php

declare(strict_types=1);

namespace App\Template\WorkEntry\Domain\Repositories;

use App\Template\WorkEntry\Domain\Entities\WorkEntry;

interface WorkEntrySaveRepository
{
    public function save(WorkEntry $workEntry): void;
}
