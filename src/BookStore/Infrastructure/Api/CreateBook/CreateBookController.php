<?php

declare(strict_types=1);

namespace App\BookStore\Infrastructure\Api\CreateBook;

use App\Shared\Api\BaseController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

final class CreateBookController extends BaseController
{
    public function __invoke(
        #[MapRequestPayload] CreateBookRequest $request,
    ): Response {
        $this->command($request->mapToCreateBookCommand());

        return new Response('', Response::HTTP_CREATED);
    }

    protected function exceptions(): array
    {
        return [];
    }
}
