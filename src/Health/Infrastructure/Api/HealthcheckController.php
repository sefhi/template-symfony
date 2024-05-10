<?php

declare(strict_types=1);

namespace App\Health\Infrastructure\Api;

use App\Health\Application\Query\GetHealthQuery;
use App\Health\Domain\DatabaseNotHealthyException;
use App\Shared\Api\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class HealthcheckController extends BaseController
{
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(
            $this->query(new GetHealthQuery())
        );
    }

    protected function exceptions(): array
    {
        return [
            DatabaseNotHealthyException::class => Response::HTTP_INTERNAL_SERVER_ERROR,
        ];
    }
}
