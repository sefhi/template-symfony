<?php

declare(strict_types=1);

namespace App\Sesame\User\Infrastructure\Api\FindUserById;

use App\Sesame\User\Application\Queries\FindUserById\FindUserByIdQuery;
use App\Sesame\User\Domain\Exceptions\UserNotFoundException;
use App\Shared\Api\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class FindUserByIdController extends BaseController
{
    public function __invoke(
        string $id,
    ): Response {
        $userResponse = $this->query(new FindUserByIdQuery($id));

        return new JsonResponse($userResponse, Response::HTTP_OK);
    }

    protected function exceptions(): array
    {
        return [
            UserNotFoundException::class => Response::HTTP_NOT_FOUND,
        ];
    }
}
