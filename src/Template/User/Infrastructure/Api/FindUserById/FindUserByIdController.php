<?php

declare(strict_types=1);

namespace App\Template\User\Infrastructure\Api\FindUserById;

use App\Shared\Api\BaseController;
use App\Template\User\Application\Queries\FindUserById\FindUserByIdQuery;
use App\Template\User\Domain\Exceptions\UserNotFoundException;
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
