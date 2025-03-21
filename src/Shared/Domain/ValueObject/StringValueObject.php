<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

abstract class StringValueObject
{
    protected function __construct(
        protected string $value,
    ) {
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function isEqualTo(self $other): bool
    {
        return $this->value() === $other->value();
    }

    abstract public static function fromString(string $value): self;
}
