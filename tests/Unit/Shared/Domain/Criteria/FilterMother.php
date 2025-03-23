<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Domain\Criteria;

use App\Shared\Domain\Criteria\Filter;
use App\Shared\Domain\Criteria\FilterField;
use App\Shared\Domain\Criteria\FilterOperator;
use App\Shared\Domain\Criteria\FilterValue;

final class FilterMother
{
    public static function create(
        ?FilterField $field = null,
        ?FilterOperator $operator = null,
        ?FilterValue $value = null,
    ): Filter {
        return Filter::create(
            $field ?? FilterMother::random(),
            $operator ?? FilterOperator::EQUAL,
            $value ?? FilterValueMother::random()
        );
    }

    public static function fromPrimitives(
        string $field,
        string $operator,
        string $value,
    ): Filter {
        return Filter::fromValues([
            'field'    => $field,
            'operator' => $operator,
            'value'    => $value,
        ]);
    }

    public static function random(): Filter
    {
        return self::create();
    }
}
