<?php

declare(strict_types=1);

namespace App\Controller;

use Shared\Application\FooCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CreateFooController extends BaseController
{
    public function __invoke(Request $request): Response
    {
        $this->commandBus->dispatch(FooCommand::create('test'));

        return new Response();
    }

    protected function exceptions(): array
    {
        return [];
    }
}
