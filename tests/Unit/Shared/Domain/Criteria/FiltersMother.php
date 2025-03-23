<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Domain\Criteria;

use App\Shared\Domain\Criteria\Filters;

final class FiltersMother
{
    public static function create(array $filters): Filters
    {
        return Filters::fromArray($filters);
    }

    public static function empty(): Filters
    {
        return self::create([]);
    }
}
