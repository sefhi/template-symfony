<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Domain\Criteria;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filters;
use App\Shared\Domain\Criteria\Order;
use Random\RandomException;

final class CriteriaMother
{
    public static function create(
        Filters $filters,
        ?Order $order = null,
        ?int $pageSize = null,
        ?int $pageNumber = null,
        ?string $cursor = null,
    ): Criteria {
        return Criteria::create(
            $filters,
            $order ?: OrderMother::none(),
            $pageSize,
            $pageNumber,
            $cursor
        );
    }

    /**
     * @throws RandomException
     */
    public static function random(): Criteria
    {
        return self::create(
            FiltersMother::create([]),
            OrderMother::random(),
            random_int(1, 100),
            random_int(1, 10)
        );
    }

    public static function criteriaPaginated(
        Criteria $criteria,
        int $pageSize,
        int $pageNumber,
    ): Criteria {
        return self::create(
            $criteria->getFilters(),
            $criteria->getOrder(),
            $pageSize,
            $pageNumber
        );
    }

    public static function withOneFilter(
        string $field,
        string $operator,
        string $value,
    ): Criteria {
        return Criteria::fromPrimitives(
            [
                [
                    'field'    => $field,
                    'operator' => $operator,
                    'value'    => $value,
                ],
            ],
            null,
            null
        );
    }

    public static function emptyPaginated(
        int $pageSize,
        int $pageNumber,
    ): Criteria {
        return self::create(
            FiltersMother::empty(),
            OrderMother::none(),
            $pageSize,
            $pageNumber
        );
    }

    public static function emptyCursor(
        string $orderBy,
        string $orderType,
        int $pageSize,
        string $cursor,
    ): Criteria {
        return self::create(
            FiltersMother::empty(),
            OrderMother::create(
                OrderByMother::create($orderBy),
                OrderTypeMother::create($orderType)
            ),
            $pageSize,
            null,
            $cursor,
        );
    }
}
