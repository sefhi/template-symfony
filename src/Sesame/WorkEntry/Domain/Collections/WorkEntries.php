<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Domain\Collections;

use App\Sesame\WorkEntry\Domain\Entities\WorkEntry;
use App\Shared\Domain\Utils\Collection;

/**
 * @extends Collection<WorkEntry>
 */
final readonly class WorkEntries extends Collection
{
    /**
     * @param array<int, WorkEntry> $workEntries
     *
     * @return self
     */
    public static function fromArray(array $workEntries): self
    {
        return new self($workEntries);
    }

    /**
     * @return array<int, WorkEntry>
     */
    public function workEntries(): array
    {
        return $this->items();
    }

    public static function empty(): self
    {
        return new self([]);
    }

    protected function type(): string
    {
        return WorkEntry::class;
    }
}
