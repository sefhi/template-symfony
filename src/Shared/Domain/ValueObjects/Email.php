<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObjects;

final readonly class Email extends StringValueObject
{
    public function __construct(string $value)
    {
        parent::__construct($value);
        $this->ensureIsValidEmail();
    }

    public function __toString(): string
    {
        return $this->value;
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
}
