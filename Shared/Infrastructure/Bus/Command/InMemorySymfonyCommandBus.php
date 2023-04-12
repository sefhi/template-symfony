<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Bus\Command;

use Shared\Domain\Bus\Command\Command;
use Shared\Domain\Bus\Command\CommandBusInterface;
use Symfony\Component\Messenger\MessageBusInterface;

final class InMemorySymfonyCommandBus implements CommandBusInterface
{
    public function __construct(private readonly MessageBusInterface $commandBus)
    {
    }

    public function dispatch(Command $command): void
    {
        $this->commandBus->dispatch($command);
    }
}
