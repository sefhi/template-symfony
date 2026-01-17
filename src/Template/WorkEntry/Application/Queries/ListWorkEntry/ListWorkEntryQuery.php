<?php

declare(strict_types=1);

namespace App\Template\WorkEntry\Application\Queries\ListWorkEntry;

use App\Shared\Domain\Bus\Query\Query;

/**
 * @see ListWorkEntryHandler
 */
final readonly class ListWorkEntryQuery implements Query
{
    public function __construct(
        public string $userId,
    ) {
    }
}
