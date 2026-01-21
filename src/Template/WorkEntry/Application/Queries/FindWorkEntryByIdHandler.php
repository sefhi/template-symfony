<?php

declare(strict_types=1);

namespace App\Template\WorkEntry\Application\Queries;

use App\Shared\Domain\Bus\Query\QueryHandler;
use App\Template\WorkEntry\Domain\Services\EnsureExistWorkEntryByIdService;
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
