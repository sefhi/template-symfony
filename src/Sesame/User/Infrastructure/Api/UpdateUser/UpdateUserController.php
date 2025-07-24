<?php

declare(strict_types=1);

namespace App\Sesame\User\Infrastructure\Api\UpdateUser;

use App\Sesame\User\Domain\Exceptions\UserNotFoundException;
use App\Shared\Api\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class UpdateUserController extends BaseController
{
    public function __invoke(
        string $id,
        #[MapRequestPayload] UpdateUserRequest $request,
    ): Response {
        $this->commandBus->command($request->toUpdateUserCommand($id));

        return new Response(status: Response::HTTP_OK);
    }

    protected function exceptions(): array
    {
        return [
            UserNotFoundException::class            => Response::HTTP_NOT_FOUND,
            UnprocessableEntityHttpException::class => Response::HTTP_BAD_REQUEST,
        ];
    }
}
