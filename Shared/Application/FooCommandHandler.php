<?php

declare(strict_types=1);

namespace Shared\Application;

use Shared\Domain\Bus\Command\CommandHandler;
use Shared\Domain\Bus\Command\CommandResponse;

final class FooCommandHandler implements CommandHandler
{
    public function __invoke(FooCommand $command): CommandResponse
    {
        return ResponseFoo::create($command->getFoo());
    }
}
