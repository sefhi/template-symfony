<?php

declare(strict_types=1);

namespace App\Sesame\User\Application\Queries\FindUserById;

use App\Sesame\User\Domain\Services\EnsureExistsUserByIdService;
use App\Shared\Domain\Bus\Query\QueryHandler;
use Ramsey\Uuid\Uuid;

final readonly class FindUserByIdHandler implements QueryHandler
{
    public function __construct(
        private EnsureExistsUserByIdService $ensureExistsUserByIdService,
    ) {
    }

    public function __invoke(FindUserByIdQuery $query): UserResponse
    {
        $userId = Uuid::fromString($query->id);

        return UserResponse::fromUser(($this->ensureExistsUserByIdService)($userId));
    }
}
