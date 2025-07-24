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
        private ?\DateTimeImmutable $startDate,
        private ?\DateTimeImmutable $endDate,
        private Timestamps $timestamps,
    ) {
    }

    public static function make(
        string $id,
        string $userId,
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $startDate = null,
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

    public static function create(
        string $id,
        string $userId,
        ?\DateTimeImmutable $startDate,
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

    public function startDate(): ?\DateTimeImmutable
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

    public function update(
        string $userId,
        \DateTimeImmutable $startDate,
        \DateTimeImmutable $createdAt,
        \DateTimeImmutable $updatedAt,
        ?\DateTimeImmutable $endDate,
        ?\DateTimeImmutable $deletedAt,
    ): void {
        $this->userId     = Uuid::fromString($userId);
        $this->startDate  = $startDate;
        $this->endDate    = $endDate;
        $this->timestamps = Timestamps::create(
            $createdAt,
            $updatedAt,
            $deletedAt,
        );
    }

    public function isDeleted(): bool
    {
        return $this->timestamps->isDeleted();
    }

    public function delete(): void
    {
        $this->timestamps = $this->timestamps->delete();
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->timestamps->createdAt();
    }

    public function updatedAt(): ?\DateTimeImmutable
    {
        return $this->timestamps->updatedAt();
    }

    public function isClockedIn(): bool
    {
        return null !== $this->startDate;
    }

    public function clockIn(?\DateTimeImmutable $startDate): void
    {
        $this->startDate = $startDate ?? new \DateTimeImmutable();
    }

    public function isClockedOut(): bool
    {
        return null !== $this->endDate && null !== $this->startDate;
    }

    public function clockOut(?\DateTimeImmutable $endDate): void
    {
        $this->endDate = $endDate ?? new \DateTimeImmutable();
    }
}
