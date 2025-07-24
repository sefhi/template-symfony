<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Infrastructure\Api\UpdateWorkEntry;

use App\Sesame\User\Domain\Exceptions\UserNotFoundException;
use App\Sesame\WorkEntry\Domain\Exceptions\WorkEntryNotFoundException;
use App\Shared\Api\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class UpdateWorkEntryController extends BaseController
{
    public function __invoke(
        string $id,
        #[MapRequestPayload] UpdateWorkEntryRequest $request,
    ): Response {
        $this->commandBus->command($request->toUpdateWorkEntryCommand($id));

        return new Response(status: Response::HTTP_OK);
    }

    protected function exceptions(): array
    {
        return [
            WorkEntryNotFoundException::class       => Response::HTTP_NOT_FOUND,
            UserNotFoundException::class            => Response::HTTP_NOT_FOUND,
            UnprocessableEntityHttpException::class => Response::HTTP_BAD_REQUEST,
        ];
    }
}
