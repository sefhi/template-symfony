<?php

namespace App\Shared\Domain\Bus\Command;

interface CommandBusSync
{
    public function dispatch(CommandSync $command): ?CommandResponse;
}
