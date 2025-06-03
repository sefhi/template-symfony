<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObjects;

use App\Shared\Domain\ValueObjects\StringValueObject;

final readonly class BookIsbn extends StringValueObject
{
    public function __construct(string $value)
    {
        parent::__construct($value);
        $this->ensureIsValidIsbn($value);
    }

    private function ensureIsValidIsbn(string $value): void
    {
        if (strlen($value) <= 0) {
            throw new \InvalidArgumentException('Invalid isbn');
        }
    }
}
