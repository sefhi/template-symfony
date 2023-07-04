<?php

declare(strict_types=1);

namespace App\Health\Infrastructure\Api;

use App\Controller\Shared\BaseController;
use App\Health\Application\Query\GetHealthQuery;
use Symfony\Component\HttpFoundation\JsonResponse;

class HealthcheckController extends BaseController
{
    public function __invoke(): JsonResponse
    {
        return new JsonResponse(
            $this->ask(new GetHealthQuery())
        );
    }

    protected function exceptions(): array
    {
        return [];
    }
}
