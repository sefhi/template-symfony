<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObjects;

final readonly class BookStock
{
    public function __construct(
        private int $value,
    ) {
    }

    public function value(): int
    {
        return $this->value;
    }

    public function equalsTo(self $other): bool
    {
        return $this->value() === $other->value();
    }
}
