<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria;

final readonly class Criteria
{
    private function __construct(
        private Filters $filters,
        private Order $order,
        private ?int $pageSize = null,
        private ?int $pageNumber = null,
        private ?string $cursor = null,
    ) {
        $this->ensurePageSizeIsRequiredWhenPageNumberIsDefined();
        $this->ensurePageSizeIsRequiredWhenCursorIsDefined();
    }

    public static function create(
        Filters $filters,
        Order $order,
        ?int $pageSize = null,
        ?int $pageNumber = null,
        ?string $cursor = null,
    ): self {
        return new self($filters, $order, $pageSize, $pageNumber, $cursor);
    }

    /**
     * @param array<array<string,mixed>> $filters
     * @param string|null                $orderBy
     * @param string|null                $orderType
     * @param int|null                   $pageSize
     * @param int|null                   $pageNumber
     * @param string|null                $cursor
     *
     * @return self
     */
    public static function fromPrimitives(
        array $filters,
        ?string $orderBy,
        ?string $orderType,
        ?int $pageSize = null,
        ?int $pageNumber = null,
        ?string $cursor = null,
    ): self {
        return new self(
            Filters::fromPrimitives($filters),
            Order::fromPrimitives($orderBy ?? null, $orderType ?? null),
            $pageSize,
            $pageNumber,
            $cursor,
        );
    }

    public function getCursor(): string
    {
        return $this->cursor ?? '';
    }

    public function getFilters(): Filters
    {
        return $this->filters;
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function hasOrder(): bool
    {
        return !$this->getOrder()->isNone();
    }

    public function hasCursor(): bool
    {
        return !empty($this->cursor);
    }

    public function hasCursorAndPageSize(): bool
    {
        return null !== $this->cursor && null !== $this->pageSize;
    }

    public function hasFilters(): bool
    {
        return $this->getFilters()->count() > 0;
    }

    public function getPageSize(): ?int
    {
        return $this->pageSize;
    }

    public function getPageNumber(): ?int
    {
        return $this->pageNumber;
    }

    public function getOffset(): int
    {
        $this->ensurePageSizeIsRequiredWhenPageNumberIsDefined();

        return ($this->pageNumber - 1) * $this->pageSize;
    }

    public function hasPagination(): bool
    {
        return null !== $this->pageSize && null !== $this->pageNumber;
    }

    private function ensurePageSizeIsRequiredWhenPageNumberIsDefined(): void
    {
        if (null !== $this->pageNumber && null === $this->pageSize) {
            throw new \InvalidArgumentException('Page number cannot be set without page size');
        }
    }

    private function ensurePageSizeIsRequiredWhenCursorIsDefined(): void
    {
        if (null !== $this->cursor && null == $this->pageSize) {
            throw new \InvalidArgumentException('Cursor cannot be set without page size');
        }
    }
}
