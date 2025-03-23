<?php

declare(strict_types=1);

namespace App\BookStore\Domain\ValueObjects;

use App\Shared\Domain\ValueObject\StringValueObject;

final readonly class BookIsbn extends StringValueObject
{

    public function __construct(string $value)
    {
        parent::__construct($value);
        $this->ensureIsValidIsbn($value);
    }

    private function ensureIsValidIsbn(string $value): void
    {
        //TODO implement isbn validation
        return;
    }
}