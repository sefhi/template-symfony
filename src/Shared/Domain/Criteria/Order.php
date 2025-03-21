<?php

declare(strict_types=1);

namespace App\Shared\Domain\Criteria;

final readonly class Order
{
    private function __construct(
        private OrderBy $orderBy,
        private OrderType $orderType,
    ) {
    }

    public static function create(OrderBy $orderBy, OrderType $orderType): self
    {
        return new self($orderBy, $orderType);
    }

    public static function none(): self
    {
        return new self(
            OrderBy::fromString(''),
            OrderType::create(OrderTypes::NONE)
        );
    }

    public static function fromPrimitives(?string $orderBy, ?string $orderType): self
    {
        return null !== $orderBy
            ? new self(
                OrderBy::fromString($orderBy),
                OrderType::create(
                    null !== $orderType
                        ? OrderTypes::from($orderType)
                        : OrderTypes::NONE
                )
            )
            : self::none();
    }

    public function getOrderBy(): OrderBy
    {
        return $this->orderBy;
    }

    public function getOrderType(): OrderType
    {
        return $this->orderType;
    }

    public function isNone(): bool
    {
        return $this->getOrderType()->isNone();
    }
}
