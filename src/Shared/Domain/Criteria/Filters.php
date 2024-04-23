<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria;

use App\Shared\Domain\Utils\Collection;

final readonly class Filters extends Collection
{
    /**
     * @param array<int, Filter> $filters
     *
     * @return self
     */
    public static function fromArray(array $filters): self
    {
        return new self($filters);
    }

    public static function fromValues(array $values): self
    {
        return new self(
            array_map(
                fn (array $value): Filter => Filter::fromValues($value),
                $values
            )
        );
    }

    public static function fromPrimitives(array $primitives): self
    {
        return new self(
            array_map(
                static fn (array $primitive): Filter => Filter::fromPrimitives(
                    $primitive['field'],
                    $primitive['operator'],
                    $primitive['value'],
                ),
                $primitives
            )
        );
    }

    /** @return array<int, Filter> */
    public function filters(): array
    {
        return $this->items();
    }

    public function hasFilters(): bool
    {
        return !empty($this->filters());
    }

    protected function type(): string
    {
        return Filter::class;
    }
}
