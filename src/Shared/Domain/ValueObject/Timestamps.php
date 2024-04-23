<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

final class Timestamps
{
    private function __construct(
        private readonly \DateTimeImmutable $createdAt,
        private readonly ?\DateTimeImmutable $updatedAt
    ) {
    }

    public static function defaultNow(): self
    {
        return new self(
            (new \DateTimeImmutable(date(\DateTimeInterface::ATOM)))->setTimezone(new \DateTimeZone('UTC')),
            null,
        );
    }

    public static function create(
        \DateTimeImmutable $createdAt,
        ?\DateTimeImmutable $updatedAt
    ): self {
        return new self(
            $createdAt,
            $updatedAt,
        );
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
