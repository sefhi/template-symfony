<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CommandHandlerController extends BaseController
{
    public function __invoke(Request $request): Response
    {
        return new Response();
    }

    protected function exceptions(): array
    {
        return [];
    }
}
