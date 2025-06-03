<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria;

use App\Shared\Domain\ValueObjects\StringValueObject;

final readonly class FilterValue extends StringValueObject
{
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
