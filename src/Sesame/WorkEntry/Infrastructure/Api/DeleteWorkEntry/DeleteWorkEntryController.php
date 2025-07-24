<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Infrastructure\Api\DeleteWorkEntry;

use App\Sesame\WorkEntry\Application\Commands\DeleteWorkEntry\DeleteWorkEntryCommand;
use App\Sesame\WorkEntry\Domain\Exceptions\WorkEntryNotFoundException;
use App\Shared\Api\BaseController;
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
