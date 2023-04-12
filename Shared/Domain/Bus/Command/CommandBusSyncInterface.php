<?php

namespace Shared\Domain\Bus\Command;

interface CommandBusSyncInterface
{
    public function dispatch(CommandSync $command): ?CommandResponse;
}
