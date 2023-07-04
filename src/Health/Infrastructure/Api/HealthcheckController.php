<?php

declare(strict_types=1);

namespace App\Health\Infrastructure\Api;

use App\Controller\Shared\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;

class HealthcheckController extends BaseController
{
    public function __invoke(): JsonResponse
    {
        $data = [
            'status'  => 'OK',
            'message' => 'The application is healthy',
        ];

        return new JsonResponse($data);
    }

    protected function exceptions(): array
    {
        return [];
    }
}
