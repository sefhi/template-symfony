<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Application\Queries;

use App\Sesame\WorkEntry\Domain\Entities\WorkEntry;
use App\Shared\Domain\Bus\Query\QueryResponse;

final readonly class WorkEntryResponse implements \JsonSerializable, QueryResponse
{
    public function __construct(
        public string $id,
        public string $userId,
        public \DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $startDate,
        public ?\DateTimeImmutable $endDate,
        public ?\DateTimeImmutable $updatedAt,
    ) {
    }

    public static function fromWorkEntry(WorkEntry $workEntry): self
    {
        return new self(
            $workEntry->id()->toString(),
            $workEntry->userId()->toString(),
            $workEntry->createdAt(),
            $workEntry->startDate(),
            $workEntry->endDate(),
            $workEntry->updatedAt(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return [
            'id'        => $this->id,
            'userId'    => $this->userId,
            'startDate' => $this->startDate?->format(\DateTimeInterface::ATOM),
            'endDate'   => $this->endDate?->format(\DateTimeInterface::ATOM),
            'createdAt' => $this->createdAt->format(\DateTimeInterface::ATOM),
            'updatedAt' => $this->updatedAt?->format(\DateTimeInterface::ATOM),
        ];
    }
}
