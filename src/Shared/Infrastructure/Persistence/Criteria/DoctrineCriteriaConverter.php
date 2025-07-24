<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Criteria;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filter;
use App\Shared\Domain\Criteria\FilterField;
use App\Shared\Domain\Criteria\FilterOperator;
use App\Shared\Domain\Criteria\Order;
use App\Shared\Domain\Criteria\OrderBy;
use App\Shared\Domain\Criteria\OrderTypes;
use Doctrine\Common\Collections\Criteria as DoctrineCriteria;
use Doctrine\Common\Collections\Order as DoctrineOrder;

final readonly class DoctrineCriteriaConverter
{
    /**
     * @param array<string, mixed> $criteriaToDoctrineFields Mapeo de campos de criterio a campos de Doctrine
     * @param array<string, mixed> $hydrators                Funciones de hidratación para convertir valores
     */
    public function __construct(
        private Criteria $criteria,
        private array $criteriaToDoctrineFields = [],
        private array $hydrators = [],
    ) {
    }

    /**
     * @param array<string, mixed> $criteriaToDoctrineFields Mapeo de campos de criterio a campos de Doctrine
     * @param array<string, mixed> $hydrators                Funciones de hidratación para convertir valores
     */
    public static function convert(
        Criteria $criteria,
        array $criteriaToDoctrineFields = [],
        array $hydrators = [],
    ): DoctrineCriteria {
        $converter = new self($criteria, $criteriaToDoctrineFields, $hydrators);

        return $converter->convertToDoctrineCriteria();
    }

    private function convertToDoctrineCriteria(): DoctrineCriteria
    {
        $doctrineCriteria = DoctrineCriteria::create();

        $this->convertToDoctrineCriteriaFilters($doctrineCriteria);
        $this->convertToDoctrineCriteriaCursor($doctrineCriteria);
        $this->convertToDoctrineCriteriaOrders($doctrineCriteria);
        $this->convertToDoctrineCriteriaPagination($doctrineCriteria);
        $this->convertToDoctrineCriteriaCursorLimited($doctrineCriteria);

        return $doctrineCriteria;
    }

    private function mapFieldValue(FilterField $field): string
    {
        return array_key_exists($field->value(), $this->criteriaToDoctrineFields)
            ? $this->criteriaToDoctrineFields[$field->value()]
            : $field->value();
    }

    private function mapOrderByValue(OrderBy $by): string
    {
        return array_key_exists($by->value(), $this->criteriaToDoctrineFields)
            ? $this->criteriaToDoctrineFields[$by->value()]
            : $by->value();
    }

    private function hydrate(mixed $field, string $value): mixed
    {
        if (!array_key_exists($field, $this->hydrators)) {
            return $value;
        }

        $hydrator = $this->hydrators[$field];

        if (\DateTimeImmutable::class === $hydrator) {
            return new \DateTimeImmutable($value);
        }

        return $this->hydrators[$field]($value);
    }

    /**
     * @param DoctrineCriteria $doctrineCriteria
     *
     * @return void
     */
    private function convertToDoctrineCriteriaFilters(DoctrineCriteria $doctrineCriteria): void
    {
        $filters = $this->criteria->getFilters();

        /** @var Filter $filter */
        foreach ($filters as $filter) {
            $expression = $doctrineCriteria->expr();

            $field = $this->mapFieldValue($filter->field());
            $value = $this->hydrate((string) $filter->field(), (string) $filter->value());
            if ($filter->operator()->equalsTo(FilterOperator::EQUAL)) {
                $doctrineCriteria->andWhere(
                    $expression->eq(
                        $field,
                        $value
                    )
                );
            } elseif ($filter->operator()->equalsTo(FilterOperator::NOT_EQUAL)) {
                $doctrineCriteria->andWhere(
                    $expression->neq(
                        $field,
                        $value
                    )
                );
            } elseif ($filter->operator()->equalsTo(FilterOperator::CONTAINS)) {
                $doctrineCriteria->andWhere(
                    $expression->contains(
                        $field,
                        $value
                    )
                );
            } elseif ($filter->operator()->equalsTo(FilterOperator::NOT_CONTAINS)) {
                $doctrineCriteria->andWhere(
                    $expression->not(
                        $expression->contains(
                            $field,
                            $value
                        )
                    )
                );
            } elseif ($filter->operator()->equalsTo(FilterOperator::GT)) {
                $doctrineCriteria->andWhere(
                    $expression->gt($field, $value)
                );
            } elseif ($filter->operator()->equalsTo(FilterOperator::GTE)) {
                $doctrineCriteria->andWhere(
                    $expression->gte($field, $value)
                );
            } elseif ($filter->operator()->equalsTo(FilterOperator::LT)) {
                $doctrineCriteria->andWhere(
                    $expression->lt($field, $value)
                );
            } elseif ($filter->operator()->equalsTo(FilterOperator::LTE)) {
                $doctrineCriteria->andWhere(
                    $expression->lte($field, $value)
                );
            } elseif ($filter->operator()->equalsTo(FilterOperator::STARTS_WITH)) {
                $doctrineCriteria->andWhere(
                    $expression->startsWith(
                        $field,
                        $value
                    )
                );
            } else {
                throw new \InvalidArgumentException('Filter operator not supported');
            }
        }
    }

    private function convertToDoctrineCriteriaOrders(DoctrineCriteria $doctrineCriteria): void
    {
        $orders = [$this->criteria->getOrder()];

        /** @var Order $order */
        foreach ($orders as $order) {
            if (!$order->isNone()) {
                $by            = $this->mapOrderByValue($order->getOrderBy());
                $doctrineOrder = $this->convertToDoctrineOrder($order->orderTypes());
                $doctrineCriteria->orderBy([$by => $doctrineOrder]);
            }
        }
    }

    /**
     * @param DoctrineCriteria $doctrineCriteria
     *
     * @return void
     */
    private function convertToDoctrineCriteriaPagination(DoctrineCriteria $doctrineCriteria): void
    {
        if ($this->criteria->hasPagination()) {
            $doctrineCriteria->setFirstResult($this->criteria->getOffset());
            $doctrineCriteria->setMaxResults($this->criteria->getPageSize());
        }
    }

    private function convertToDoctrineCriteriaCursor(DoctrineCriteria $doctrineCriteria): void
    {
        if ($this->criteria->hasCursor() && $this->criteria->hasOrder()) {
            $field         = $this->criteria->getOrder()->getOrderBy();
            $orderBy       = $this->mapOrderByValue($field);
            $cursor        = $this->criteria->getCursor();
            $cursorHydrate = $this->hydrate((string) $field, $cursor);
            $orderTypes    = $this->criteria->getOrder()->getOrderType()->getOrderTypes();
            $expression    = $doctrineCriteria->expr();

            if ($orderTypes->equalsTo(OrderTypes::ASC)) {
                $condition = $expression->gt($orderBy, $cursorHydrate);
            } else {
                $condition = $expression->lt($orderBy, $cursorHydrate);
            }

            $doctrineCriteria->andWhere($condition);
        }
    }

    private function convertToDoctrineCriteriaCursorLimited(DoctrineCriteria $doctrineCriteria): void
    {
        if ($this->criteria->hasCursorAndPageSize()) {
            $doctrineCriteria->setMaxResults($this->criteria->getPageSize());
        }
    }

    private function convertToDoctrineOrder(OrderTypes $orderType): DoctrineOrder
    {
        return match ($orderType) {
            OrderTypes::ASC  => DoctrineOrder::Ascending,
            OrderTypes::DESC => DoctrineOrder::Descending,
            default          => DoctrineOrder::Ascending,
        };
    }
}
