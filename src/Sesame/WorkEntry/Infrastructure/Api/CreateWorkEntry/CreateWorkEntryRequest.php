<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Infrastructure\Api\CreateWorkEntry;

use App\Sesame\WorkEntry\Application\Commands\CreateWorkEntry\CreateWorkEntryCommand;
use Symfony\Component\Validator\Constraints as Assert;

final class CreateWorkEntryRequest
{
    public function __construct(
        #[Assert\NotBlank(message: '<id> is required')]
        #[Assert\Uuid(message: '<id> must be a valid UUID')]
        public string $id,
        #[Assert\NotBlank(message: '<userId> is required')]
        #[Assert\Uuid(message: '<userId> must be a valid UUID')]
        public string $userId,
        #[Assert\NotBlank(message: '<createdAt> is required')]
        #[Assert\DateTime(format: \DateTimeInterface::ATOM, message: '<createdAt> must be a valid date time in the format ' . \DateTimeInterface::ATOM)]
        public string $createdAt,
        #[Assert\DateTime(format: \DateTimeInterface::ATOM, message: '<startDate> must be a valid date time in the format ' . \DateTimeInterface::ATOM)]
        public ?string $startDate = null,
    ) {
    }

    public function toCreateWorkEntryCommand(): CreateWorkEntryCommand
    {
        return new CreateWorkEntryCommand(
            $this->id,
            $this->userId,
            new \DateTimeImmutable($this->createdAt),
            $this->startDate
                ? new \DateTimeImmutable($this->startDate)
                : null,
        );
    }
}
