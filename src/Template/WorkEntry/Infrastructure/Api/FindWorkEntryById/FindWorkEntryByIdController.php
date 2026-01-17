<?php

declare(strict_types=1);

namespace App\Template\WorkEntry\Infrastructure\Api\FindWorkEntryById;

use App\Template\WorkEntry\Application\Queries\FindWorkEntryByIdQuery;
use App\Template\WorkEntry\Domain\Exceptions\WorkEntryNotFoundException;
use App\Shared\Api\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class FindWorkEntryByIdController extends BaseController
{
    public function __invoke(
        string $id,
    ): Response {
        $workEntryResponse = $this->query(new FindWorkEntryByIdQuery($id));

        return new JsonResponse($workEntryResponse, Response::HTTP_OK);
    }

    protected function exceptions(): array
    {
        return [
            WorkEntryNotFoundException::class => Response::HTTP_NOT_FOUND,
        ];
    }
}
