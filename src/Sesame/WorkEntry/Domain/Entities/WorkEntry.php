<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Domain\Entities;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\ValueObjects\Timestamps;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

final class WorkEntry extends AggregateRoot
{
    private function __construct(
        private UuidInterface $id,
        private UuidInterface $userId,
        private \DateTimeImmutable $startDate,
        private ?\DateTimeImmutable $endDate,
        private Timestamps $timestamps,
    ) {
    }

    public static function make(
        string $id,
        string $userId,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $endDate = null,
        ?\DateTimeImmutable $updatedAt = null,
        ?\DateTimeImmutable $deletedAt = null,
    ): self {
        return new self(
            Uuid::fromString($id),
            Uuid::fromString($userId),
            $startDate,
            $endDate,
            Timestamps::create(
                $createdAt,
                $updatedAt,
                $deletedAt,
            )
        );
    }

    public static function start(
        string $id,
        string $userId,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $createdAt,
    ): self {
        return new self(
            Uuid::fromString($id),
            Uuid::fromString($userId),
            $startDate,
            null,
            Timestamps::create($createdAt)
        );
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function userId(): UuidInterface
    {
        return $this->userId;
    }

    public function startDate(): \DateTimeImmutable
    {
        return $this->startDate;
    }

    public function endDate(): ?\DateTimeImmutable
    {
        return $this->endDate;
    }

    public function timestamps(): Timestamps
    {
        return $this->timestamps;
    }
}
