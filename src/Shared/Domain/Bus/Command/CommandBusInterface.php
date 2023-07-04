<?php

namespace App\Shared\Domain\Bus\Command;

interface CommandBusInterface
{
    public function dispatch(Command $command): ?CommandResponse;
}
