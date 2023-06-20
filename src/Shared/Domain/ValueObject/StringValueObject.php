<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

abstract class StringValueObject
{
    abstract public function __toString(): string;

    abstract public function value(): string;

    public function isEqualTo(self $other): bool
    {
        return $this->value() === $other->value();
    }

    abstract public static function fromString(string $value): self;
}
