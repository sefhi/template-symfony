<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus\Command;

use App\Shared\Domain\Bus\Command\CommandBusSync;
use App\Shared\Domain\Bus\Command\CommandResponse;
use App\Shared\Domain\Bus\Command\CommandSync;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class InMemorySymfonyCommandBusSync implements CommandBusSync
{
    public function __construct(private readonly MessageBusInterface $commandBusSync)
    {
    }

    public function dispatch(CommandSync $command): ?CommandResponse
    {
        return $this->commandBusSync->dispatch($command)->last(HandledStamp::class)?->getResult();
    }
}
