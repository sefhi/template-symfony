<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Domain\Criteria;

use App\Shared\Domain\Criteria\OrderBy;

final class OrderByMother
{
    public static function create(string $value): OrderBy
    {
        return OrderBy::fromString($value);
    }

    public static function random(): OrderBy
    {
        return self::create('name');
    }
}
