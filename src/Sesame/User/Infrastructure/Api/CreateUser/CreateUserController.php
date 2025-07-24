<?php

declare(strict_types=1);

namespace App\Sesame\User\Infrastructure\Api\CreateUser;

use App\Shared\Api\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class CreateUserController extends BaseController
{
    public function __invoke(
        #[MapRequestPayload] CreateUserRequest $request,
    ): Response {
        $this->commandBus->command($request->toCreateUserCommand());

        return new Response(status: Response::HTTP_CREATED);
    }

    protected function exceptions(): array
    {
        return [
            UnprocessableEntityHttpException::class => Response::HTTP_BAD_REQUEST,
        ];
    }
}
