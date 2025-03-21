<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Criteria;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filter;
use App\Shared\Domain\Criteria\FilterField;
use App\Shared\Domain\Criteria\FilterOperator;
use App\Shared\Domain\Criteria\OrderBy;
use App\Shared\Domain\Criteria\OrderTypes;

final class SqlCriteriaConverter
{
    /**
     * @param array<string>         $fieldToSelect       Campos a seleccionar en la consulta SQL
     * @param array<string, string> $criteriaToSqlFields Mapeo de campos de criterio a campos SQL
     */
    public function convert(
        array $fieldToSelect,
        string $tableName,
        Criteria $criteria,
        array $criteriaToSqlFields = [],
    ): string {
        $query = 'SELECT ' . implode(', ', $fieldToSelect) . ' FROM ' . $tableName;

        if ($criteria->hasFilters()) {
            $query .= ' WHERE ';

            /** @var Filter $filter */
            foreach ($criteria->getFilters() as $key => $filter) {
                if ($key > 0) {
                    $query .= ' AND ';
                }

                $field = $this->mapFieldValue($filter->field(), $criteriaToSqlFields);

                if ($filter->operator()->equalsTo(FilterOperator::CONTAINS)) {
                    $query .= "$field LIKE '%{$filter->value()}%'";
                } elseif ($filter->operator()->equalsTo(FilterOperator::STARTS_WITH)) {
                    $query .= "$field LIKE '{$filter->value()}%'";
                } else {
                    $query .= "$field {$filter->operator()->operatorSql()} '{$filter->value()}'";
                }
            }
        }

        if ($criteria->hasCursor()) {
            $orderBy    = $this->mapOrderByValue($criteria->getOrder()->getOrderBy(), $criteriaToSqlFields);
            $cursor     = $criteria->getCursor();
            $orderTypes = $criteria->getOrder()->getOrderType()->getOrderTypes();

            $condition = "$orderBy {$this->operatorForCursor($orderTypes)} '$cursor'";

            if (str_contains($query, 'WHERE')) {
                $query .= " AND $condition";
            } else {
                $query .= " WHERE $condition";
            }
        }

        if ($criteria->hasOrder()) {
            $order   = $criteria->getOrder();
            $orderBy = $this->mapOrderByValue($order->getOrderBy(), $criteriaToSqlFields);
            $query .= ' ORDER BY ' . $orderBy . ' ' . $order->getOrderType()->value();
        }

        if ($criteria->hasPagination()) {
            $query .= ' LIMIT ' . $criteria->getPageSize();
            $query .= ' OFFSET ' . $criteria->getOffset();
        }

        if ($criteria->hasCursorAndPageSize()) {
            $query .= ' LIMIT ' . $criteria->getPageSize();
        }

        return $query;
    }

    /**
     * @param array<string, string> $criteriaToSqlFields
     */
    private function mapFieldValue(FilterField $field, array $criteriaToSqlFields = []): string
    {
        return array_key_exists($field->value(), $criteriaToSqlFields)
            ? $criteriaToSqlFields[$field->value()]
            : $field->value();
    }

    /**
     * @param array<string, string> $criteriaToSqlFields
     */
    private function mapOrderByValue(OrderBy $by, array $criteriaToSqlFields = []): string
    {
        return array_key_exists($by->value(), $criteriaToSqlFields)
            ? $criteriaToSqlFields[$by->value()]
            : $by->value();
    }

    private function operatorForCursor(OrderTypes $orderTypes): string
    {
        return $orderTypes->equalsTo(OrderTypes::ASC) ? '>' : '<';
    }
}
