<?php

declare(strict_types=1);

namespace Shared\Infrastructure\Bus\Query;

use Shared\Domain\Bus\Query\Query;
use Shared\Domain\Bus\Query\QueryBusInterface;
use Shared\Domain\Bus\Query\QueryResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class InMemorySymfonyQueryBus implements QueryBusInterface
{
    public function __construct(private readonly MessageBusInterface $queryBus)
    {
    }

    public function ask(Query $query): ?QueryResponse
    {
        return $this->queryBus->dispatch($query)->last(HandledStamp::class)?->getResult();
    }
}
