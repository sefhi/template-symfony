<?php

declare(strict_types=1);

namespace App\Template\User\Infrastructure\Api\DeleteUser;

use App\Shared\Api\BaseController;
use App\Template\User\Application\Commands\DeleteUser\DeleteUserCommand;
use App\Template\User\Domain\Exceptions\UserNotFoundException;
use Symfony\Component\HttpFoundation\Response;

final class DeleteUserController extends BaseController
{
    public function __invoke(
        string $id,
    ): Response {
        $this->commandBus->command(new DeleteUserCommand($id));

        return new Response(status: Response::HTTP_NO_CONTENT);
    }

    protected function exceptions(): array
    {
        return [
            UserNotFoundException::class => Response::HTTP_NOT_FOUND,
        ];
    }
}
