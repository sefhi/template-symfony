<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Application\Queries;

use App\Sesame\WorkEntry\Domain\Services\EnsureExistWorkEntryByIdService;
use App\Shared\Domain\Bus\Query\QueryHandler;
use Ramsey\Uuid\Uuid;

final readonly class FindWorkEntryByIdHandler implements QueryHandler
{
    public function __construct(
        private EnsureExistWorkEntryByIdService $ensureExistWorkEntryByIdService,
    ) {
    }

    public function __invoke(FindWorkEntryByIdQuery $query): WorkEntryResponse
    {
        $workEntryId = Uuid::fromString($query->id);

        return WorkEntryResponse::fromWorkEntry(
            ($this->ensureExistWorkEntryByIdService)($workEntryId)
        );
    }
}
