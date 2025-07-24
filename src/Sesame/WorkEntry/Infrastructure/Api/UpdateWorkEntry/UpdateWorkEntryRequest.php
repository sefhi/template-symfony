<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Infrastructure\Api\UpdateWorkEntry;

use App\Sesame\WorkEntry\Application\Commands\UpdateWorkEntry\UpdateWorkEntryCommand;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateWorkEntryRequest
{
    public function __construct(
        #[Assert\NotBlank(message: '<userId> is required')]
        #[Assert\Uuid(message: '<userId> must be a valid UUID')]
        public string $userId,
        #[Assert\NotBlank(message: '<startDate> is required')]
        #[Assert\DateTime(format: \DateTimeInterface::ATOM, message: '<startDate> must be a valid date time in the format ' . \DateTimeInterface::ATOM)]
        public string $startDate,
        #[Assert\NotBlank(message: '<createdAt> is required')]
        #[Assert\DateTime(format: \DateTimeInterface::ATOM, message: '<createdAt> must be a valid date time in the format ' . \DateTimeInterface::ATOM)]
        public string $createdAt,
        #[Assert\DateTime(format: \DateTimeInterface::ATOM, message: '<updateAt> must be a valid date time in the format ' . \DateTimeInterface::ATOM)]
        public ?string $updatedAt = null,
        #[Assert\DateTime(format: \DateTimeInterface::ATOM, message: '<endDate> must be a valid date time in the format ' . \DateTimeInterface::ATOM)]
        public ?string $endDate = null,
        #[Assert\DateTime(format: \DateTimeInterface::ATOM, message: '<deletedAt> must be a valid date time in the format ' . \DateTimeInterface::ATOM)]
        public ?string $deletedAt = null,
    ) {
    }

    public function toUpdateWorkEntryCommand(string $id): UpdateWorkEntryCommand
    {
        return new UpdateWorkEntryCommand(
            id: $id,
            userId: $this->userId,
            startDate: new \DateTimeImmutable($this->startDate),
            createdAt: new \DateTimeImmutable($this->createdAt),
            updatedAt: $this->updatedAt
                ? new \DateTimeImmutable($this->updatedAt)
                : new \DateTimeImmutable(),
            endDate: $this->endDate
                ? new \DateTimeImmutable($this->endDate)
                : null,
            deletedAt: $this->deletedAt
                ? new \DateTimeImmutable($this->deletedAt)
                : null,
        );
    }
}
