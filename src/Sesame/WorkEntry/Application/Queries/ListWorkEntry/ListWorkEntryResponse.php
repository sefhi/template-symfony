<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Application\Queries\ListWorkEntry;

use App\Sesame\WorkEntry\Application\Queries\WorkEntryResponse;
use App\Sesame\WorkEntry\Domain\Collections\WorkEntries;
use App\Sesame\WorkEntry\Domain\Entities\WorkEntry;
use App\Shared\Domain\Bus\Query\QueryResponse;

final readonly class ListWorkEntryResponse implements QueryResponse, \JsonSerializable
{
    /**
     * @param array<int, WorkEntryResponse> $workEntries
     */
    public function __construct(
        public array $workEntries,
    ) {
    }

    public static function fromWorkEntries(WorkEntries $workEntries): self
    {
        return new self(
            array_map(
                fn (WorkEntry $workEntry): WorkEntryResponse => WorkEntryResponse::fromWorkEntry($workEntry),
                $workEntries->workEntries()
            )
        );
    }

    /**
     * @return WorkEntryResponse[]
     */
    public function jsonSerialize(): array
    {
        return $this->workEntries;
    }
}
