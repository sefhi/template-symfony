<?php

namespace Shared\Domain\Bus\Command;

interface CommandBusInterface
{
    public function dispatch(Command $command): void;
}
