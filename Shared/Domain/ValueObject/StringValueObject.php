<?php

declare(strict_types=1);

namespace Shared\Domain\ValueObject;

abstract class StringValueObject
{
    public function __construct(protected string $value)
    {
    }

    public function __toString(): string
    {
        return $this->value;
    }

    abstract public function value(): string;

    public function isEqualTo(self $other): bool
    {
        return $this->value() === $other->value();
    }

    abstract public static function fromString(string $value): self;
}
