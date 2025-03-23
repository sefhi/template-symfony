<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Domain\Criteria;

use App\Shared\Domain\Criteria\OrderType;
use App\Shared\Domain\Criteria\OrderTypes;

final class OrderTypeMother
{
    public static function create(string $value): OrderType
    {
        return OrderType::create(OrderTypes::from($value));
    }

    public static function random(): OrderType
    {
        return self::create(OrderTypes::DESC->value);
    }

    public static function none(): OrderType
    {
        return self::create(OrderTypes::NONE->value);
    }
}
