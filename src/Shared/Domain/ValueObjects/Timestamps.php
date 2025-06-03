<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObjects;

final readonly class Timestamps
{
    public function __construct(
        private \DateTimeImmutable $createdAt,
        private ?\DateTimeImmutable $updatedAt,
        private ?\DateTimeImmutable $deletedAt,
    ) {
    }

    public static function defaultNow(): self
    {
        return new self(
            new \DateTimeImmutable(date(\DateTimeInterface::ATOM))->setTimezone(new \DateTimeZone('UTC')),
            null,
            null,
        );
    }

    public static function create(
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $updatedAt = null,
        ?\DateTimeImmutable $deletedAt = null,
    ): self {
        return new self(
            $createdAt,
            $updatedAt,
            $deletedAt,
        );
    }

    public function delete(): self
    {
        return new self(
            $this->createdAt,
            $this->updatedAt,
            new \DateTimeImmutable(date(\DateTimeInterface::ATOM))->setTimezone(new \DateTimeZone('UTC')),
        );
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function deletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }
}
