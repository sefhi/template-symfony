<?php

declare(strict_types=1);

namespace App\Controller;

use Shared\Application\TestCommand;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class CommandHandlerController extends BaseController
{
    public function __invoke(Request $request): Response
    {
        $this->commandBus->dispatch(TestCommand::create('test'));
        return new Response();
    }

    protected function exceptions(): array
    {
        return [];
    }
}
