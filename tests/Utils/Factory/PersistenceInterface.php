<?php

namespace Tests\Utils\Factory;

use App\Shared\Domain\Aggregate\AggregateRoot;

interface PersistenceInterface
{
    public function persist(AggregateRoot $entity): void;
}
