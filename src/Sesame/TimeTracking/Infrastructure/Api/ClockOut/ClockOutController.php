<?php

declare(strict_types=1);

namespace App\Sesame\TimeTracking\Infrastructure\Api\ClockOut;

use App\Sesame\TimeTracking\Domain\Exceptions\WorkEntryAlreadyClockedOutException;
use App\Sesame\TimeTracking\Domain\Exceptions\WorkEntryNotBelongToUserException;
use App\Sesame\TimeTracking\Domain\Exceptions\WorkEntryNotClockedInException;
use App\Sesame\WorkEntry\Domain\Exceptions\WorkEntryNotFoundException;
use App\Shared\Api\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

final class ClockOutController extends BaseController
{
    public function __invoke(
        string $id,
        #[MapRequestPayload] ClockOutRequest $request,
    ): Response {
        $user = $this->authenticatedUserProvider->currentUser();
        $this->commandBus->command($request->toClockOutCommand(workEntryId: $id, userId: $user->id()->toString()));

        return new Response(status: Response::HTTP_OK);
    }

    protected function exceptions(): array
    {
        return [
            WorkEntryNotFoundException::class          => Response::HTTP_NOT_FOUND,
            WorkEntryAlreadyClockedOutException::class => Response::HTTP_BAD_REQUEST,
            WorkEntryNotClockedInException::class      => Response::HTTP_BAD_REQUEST,
            WorkEntryNotBelongToUserException::class   => Response::HTTP_FORBIDDEN,
            UnprocessableEntityHttpException::class    => Response::HTTP_BAD_REQUEST,
        ];
    }
}
