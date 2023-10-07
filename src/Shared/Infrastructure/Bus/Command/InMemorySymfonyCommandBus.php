<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus\Command;

use App\Shared\Domain\Bus\Command\Command;
use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\Bus\Command\CommandResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;

final class InMemorySymfonyCommandBus implements CommandBus
{
    use HandleTrait;

    public function __construct(private readonly MessageBusInterface $commandBus)
    {
        $this->messageBus = $this->commandBus;
    }

    public function command(Command $command): ?CommandResponse
    {
        return $this->handle($command);
    }
}
