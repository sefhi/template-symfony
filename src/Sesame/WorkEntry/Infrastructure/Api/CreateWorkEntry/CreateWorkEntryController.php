<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Infrastructure\Api\CreateWorkEntry;

use App\Sesame\User\Domain\Exceptions\UserNotFoundException;
use App\Shared\Api\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class CreateWorkEntryController extends BaseController
{
    public function __invoke(
        #[MapRequestPayload] CreateWorkEntryRequest $request,
    ): Response {
        $this->commandBus->command($request->toCreateWorkEntryCommand());

        return new Response(status: Response::HTTP_CREATED);
    }

    protected function exceptions(): array
    {
        return [
            UserNotFoundException::class            => Response::HTTP_NOT_FOUND,
            UnprocessableEntityHttpException::class => Response::HTTP_BAD_REQUEST,
        ];
    }
}
