<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria;

use App\Shared\Domain\ValueObject\StringValueObject;

final class OrderBy extends StringValueObject
{
    public static function fromString(string $value): OrderBy
    {
        return new self($value);
    }
}
