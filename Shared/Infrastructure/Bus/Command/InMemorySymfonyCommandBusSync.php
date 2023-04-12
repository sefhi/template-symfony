<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Bus\Command;

use Shared\Domain\Bus\Command\CommandBusSyncInterface;
use Shared\Domain\Bus\Command\CommandResponse;
use Shared\Domain\Bus\Command\CommandSync;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class InMemorySymfonyCommandBusSync implements CommandBusSyncInterface
{
    public function __construct(private readonly MessageBusInterface $commandBusSync)
    {
    }

    public function dispatch(CommandSync $command): ?CommandResponse
    {
        return $this->commandBusSync->dispatch($command)->last(HandledStamp::class)?->getResult();
    }
}
