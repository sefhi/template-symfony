<?php

declare(strict_types=1);

namespace Shared\Application;

use Shared\Domain\Bus\Command\CommandResponse;

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
}
