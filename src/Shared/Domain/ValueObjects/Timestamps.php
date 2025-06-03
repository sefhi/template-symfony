<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObjects;

final readonly class Timestamps
{
    public function __construct(
        private \DateTimeImmutable $createdAt,
        private ?\DateTimeImmutable $updatedAt,
    ) {
    }

    public static function defaultNow(): self
    {
        return new self(
            new \DateTimeImmutable(date(\DateTimeInterface::ATOM))->setTimezone(new \DateTimeZone('UTC')),
            null
        );
    }

    public static function create(
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $updatedAt,
    ): self {
        return new self(
            $createdAt,
            $updatedAt,
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
}
