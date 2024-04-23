<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria;

enum FilterOperator: string
{
    case EQUAL        = 'eq';
    case NOT_EQUAL    = 'neq';
    case GT           = 'gt';
    case GTE          = 'gte';
    case LT           = 'lt';
    case LTE          = 'lte';
    case CONTAINS     = 'CONTAINS';
    case NOT_CONTAINS = 'NOT_CONTAINS';
    case STARTS_WITH  = 'STARTS_WITH';

    public function equalsTo(self $operator): bool
    {
        return $this->value === $operator->value;
    }

    public function operatorSql(): string
    {
        return match ($this->value) {
            self::EQUAL->value     => '=',
            self::NOT_EQUAL->value => '!=',
            self::GT->value        => '>',
            self::GTE->value       => '>=',
            self::LT->value        => '<',
            self::LTE->value       => '<=',
            self::CONTAINS->value, self::STARTS_WITH->value => 'LIKE',
            self::NOT_CONTAINS->value => 'NOT LIKE',
        };
    }
}
