<?php

namespace App\Shared\Domain\Bus\Query;

interface QueryBus
{
    public function query(Query $query): ?QueryResponse;
}
