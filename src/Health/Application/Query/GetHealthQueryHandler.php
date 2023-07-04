<?php

declare(strict_types=1);

namespace App\Health\Application\Query;

use App\Health\Application\Query\Response\GetHealthQueryResponse;
use App\Shared\Domain\Bus\Query\QueryHandler;
use App\Shared\Domain\Bus\Query\QueryResponse;

final class GetHealthQueryHandler implements QueryHandler
{
    public function __invoke(GetHealthQuery $query): QueryResponse
    {
        return GetHealthQueryResponse::create(
            'OK',
            'The application is healthy'
        );
    }
}
