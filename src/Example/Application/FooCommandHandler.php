<?php

declare(strict_types=1);

namespace App\Example\Application;

use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shared\Domain\Bus\Command\CommandResponse;

final class FooCommandHandler implements CommandHandler
{
    public function __invoke(FooCommand $command): CommandResponse
    {
        return ResponseFoo::create($command->getFoo());
    }
}
