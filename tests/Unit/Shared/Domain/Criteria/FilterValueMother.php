<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Domain\Criteria;

use App\Shared\Domain\Criteria\FilterValue;

final class FilterValueMother
{
    public static function create(string $value): FilterValue
    {
        return FilterValue::fromString($value);
    }

    public static function random(): FilterValue
    {
        return self::create('name');
    }
}
