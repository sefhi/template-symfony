<?php

declare(strict_types=1);

namespace Tests\Utils\Factory;

use App\Shared\Domain\Aggregate\AggregateRoot;

abstract class AbstractFactory
{
    public function __construct(
        protected readonly PersistenceInterface $persistence,
    ) {
    }

    abstract public function createOne(AggregateRoot $entity): void;

    abstract public function createMany(int $count = 5): void;
}
