<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Domain\Criteria;

use App\Shared\Domain\Criteria\Order;
use App\Shared\Domain\Criteria\OrderBy;
use App\Shared\Domain\Criteria\OrderType;

final class OrderMother
{
    public static function create(OrderBy $orderBy, OrderType $orderType): Order
    {
        return Order::create($orderBy, $orderType);
    }

    public static function none(): Order
    {
        return self::create(
            OrderBy::fromString(''),
            OrderTypeMother::none()
        );
    }

    public static function random(): Order
    {
        return self::create(
            OrderByMother::random(),
            OrderTypeMother::random()
        );
    }

    public static function withOneSorted(string $orderBy, string $sort): Order
    {
        return self::create(
            OrderBy::fromString($orderBy),
            OrderType::fromString($sort)
        );
    }
}
