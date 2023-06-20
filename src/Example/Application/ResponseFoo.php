<?php

declare(strict_types=1);

namespace App\Example\Application;

use App\Shared\Domain\Bus\Command\CommandResponse;

final class ResponseFoo implements CommandResponse
{
    private function __construct(
        private readonly string $foo
    ) {
    }

    public static function create(string $foo): self
    {
        return new self($foo);
    }

/**
 * @return string
 */
public function getFoo(): string
{
    return $this->foo;
}
}
