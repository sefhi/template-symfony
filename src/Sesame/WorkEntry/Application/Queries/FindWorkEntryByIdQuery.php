<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Application\Queries;

use App\Shared\Domain\Bus\Query\Query;

/**
 * @see FindWorkEntryByIdHandler
 */
final readonly class FindWorkEntryByIdQuery implements Query
{
    public function __construct(
        public string $id,
    ) {
    }
}
