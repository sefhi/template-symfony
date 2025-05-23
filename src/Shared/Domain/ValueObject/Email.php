<?php

declare(strict_types=1);

namespace App\Shared\Domain\ValueObject;

final readonly class Email extends StringValueObject
{
    public function __construct(protected string $value)
    {
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
