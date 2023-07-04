<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Bus\Query;

use App\Shared\Domain\Bus\Query\Query;
use App\Shared\Domain\Bus\Query\QueryBusInterface;
use App\Shared\Domain\Bus\Query\QueryResponse;
use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class InMemorySymfonyQueryBus implements QueryBusInterface
{
    use HandleTrait;

    public function __construct(private readonly MessageBusInterface $queryBus)
    {
        $this->messageBus = $this->queryBus;
    }

    public function ask(Query $query): ?QueryResponse
    {
        return $this->handle($query);
    }
}
