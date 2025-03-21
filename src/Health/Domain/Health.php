<?php

declare(strict_types=1);

namespace App\Health\Domain;

final readonly class Health
{
    private function __construct(
        private int $status,
    ) {
    }

    public static function create(int $status): self
    {
        return new self($status);
    }

    public function status(): int
    {
        return $this->status;
    }
}
