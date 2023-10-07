<?php

namespace App\Shared\Domain\Bus\Command;

interface CommandBus
{
    public function command(Command $command): ?CommandResponse;
}
