<?php

declare(strict_types=1);

namespace Shared\Application;

use Shared\Domain\Bus\Command\CommandHandler;
use Shared\Domain\Bus\Command\CommandResponse;

final class TestCommandHandler implements CommandHandler
{
    public function __invoke(TestCommand $command): CommandResponse
    {
        $test = '';
    }
}
