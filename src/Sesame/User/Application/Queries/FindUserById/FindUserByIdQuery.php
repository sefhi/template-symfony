<?php

declare(strict_types=1);

namespace App\Sesame\User\Application\Queries\FindUserById;

use App\Shared\Domain\Bus\Query\Query;

/**
 * @see FindUserByIdHandler
 */
final readonly class FindUserByIdQuery implements Query
{
    public function __construct(
        public string $id,
    ) {
    }
}
