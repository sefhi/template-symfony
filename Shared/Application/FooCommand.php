<?php

declare(strict_types=1);

namespace Shared\Application;

use Shared\Domain\Bus\Command\Command;

final class FooCommand implements Command
{
    private function __construct(
        private readonly string $foo,
    ) {
    }

    public static function create(string $foo): self
    {
        return new self($foo);
    }

    public function getFoo(): string
    {
        return $this->foo;
    }
}
