<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class HealthcheckController
{
    /**
     * @Route("/healthcheck", name="healthcheck")
     */
    public function __invoke(): JsonResponse
    {
        $data = [
            'status'  => 'OK',
            'message' => 'The application is healthy',
        ];

        return new JsonResponse($data);
    }
}
