<?php

declare(strict_types=1);

namespace App\Template\WorkEntry\Application\Queries\ListWorkEntry;

use App\Shared\Domain\Bus\Query\QueryResponse;
use App\Template\WorkEntry\Application\Queries\WorkEntryResponse;
use App\Template\WorkEntry\Domain\Collections\WorkEntries;
use App\Template\WorkEntry\Domain\Entities\WorkEntry;

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
