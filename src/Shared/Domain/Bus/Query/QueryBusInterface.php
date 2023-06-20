<?php

namespace App\Shared\Domain\Bus\Query;

interface QueryBusInterface
{
    public function ask(Query $query): ?QueryResponse;
}
