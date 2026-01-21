<?php

declare(strict_types=1);

namespace App\Template\WorkEntry\Infrastructure\Api\DeleteWorkEntry;

use App\Shared\Api\BaseController;
use App\Template\WorkEntry\Application\Commands\DeleteWorkEntry\DeleteWorkEntryCommand;
use App\Template\WorkEntry\Domain\Exceptions\WorkEntryNotFoundException;
use Symfony\Component\HttpFoundation\Response;

final class DeleteWorkEntryController extends BaseController
{
    public function __invoke(
        string $id,
    ): Response {
        $this->commandBus->command(new DeleteWorkEntryCommand($id));

        return new Response(status: Response::HTTP_NO_CONTENT);
    }

    protected function exceptions(): array
    {
        return [
            WorkEntryNotFoundException::class => Response::HTTP_NOT_FOUND,
        ];
    }
}
