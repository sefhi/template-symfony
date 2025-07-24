<?php

declare(strict_types=1);

namespace App\Sesame\User\Infrastructure\Api\DeleteUser;

use App\Sesame\User\Application\Commands\DeleteUser\DeleteUserCommand;
use App\Sesame\User\Domain\Exceptions\UserNotFoundException;
use App\Shared\Api\BaseController;
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
