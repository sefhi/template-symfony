<?php

declare(strict_types=1);

namespace Shared\Domain\ValueObject;

final class Email extends StringValueObject
{
    private function __construct(private readonly string $value)
    {
        $this->ensureIsValidEmail();
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    private function ensureIsValidEmail(): void
    {
        if (filter_var($this->value(), FILTER_VALIDATE_EMAIL)) {
            return;
        }

        throw new \InvalidArgumentException('Email not is valid');
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
