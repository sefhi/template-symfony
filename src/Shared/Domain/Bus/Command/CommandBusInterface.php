<?php

namespace App\Shared\Domain\Bus\Command;

interface CommandBusInterface
{
    public function command(Command $command): ?CommandResponse;
}
