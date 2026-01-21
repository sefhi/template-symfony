<?php

declare(strict_types=1);

namespace App\Template\WorkEntry\Infrastructure\Api\ListWorkEntry;

use App\Shared\Api\BaseController;
use App\Template\WorkEntry\Application\Queries\ListWorkEntry\ListWorkEntryQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

final class ListWorkEntryController extends BaseController
{
    public function __invoke(): Response
    {
        $user = $this->authenticatedUserProvider->currentUser();

        $workEntriesResponse = $this->query(
            new ListWorkEntryQuery(
                $user->id()->toString(),
            )
        );

        return new JsonResponse($workEntriesResponse, Response::HTTP_OK);
    }

    protected function exceptions(): array
    {
        return [
            UnauthorizedHttpException::class => Response::HTTP_UNAUTHORIZED,

        ];
    }
}
