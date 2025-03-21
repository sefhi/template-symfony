<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria;

final readonly class Filter
{
    private function __construct(
        private FilterField $field,
        private FilterOperator $operator,
        private FilterValue $value,
    ) {
    }

    /**
     * @param array<string, mixed> $values
     *
     * @return self
     */
    public static function fromValues(array $values): self
    {
        return new self(
            FilterField::fromString($values['field']),
            FilterOperator::from($values['operator']),
            FilterValue::fromString($values['value'])
        );
    }

    public static function fromPrimitives(string $field, string $operator, string $value): self
    {
        return new self(
            FilterField::fromString($field),
            FilterOperator::from($operator),
            FilterValue::fromString($value)
        );
    }

    public static function create(
        FilterField $field,
        FilterOperator $operator,
        FilterValue $value,
    ): self {
        return new self($field, $operator, $value);
    }

    public function field(): FilterField
    {
        return $this->field;
    }

    public function operator(): FilterOperator
    {
        return $this->operator;
    }

    public function value(): FilterValue
    {
        return $this->value;
    }
}
