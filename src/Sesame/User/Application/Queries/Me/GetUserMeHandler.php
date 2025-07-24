<?php

declare(strict_types=1);

namespace App\Sesame\User\Application\Queries\Me;

use App\Sesame\User\Application\Queries\FindUserById\UserResponse;
use App\Sesame\User\Domain\Security\AuthenticatedUserProvider;
use App\Shared\Domain\Bus\Query\QueryHandler;

final readonly class GetUserMeHandler implements QueryHandler
{
    public function __construct(
        private AuthenticatedUserProvider $authenticatedUserProvider,
    ) {
    }

    public function __invoke(GetUserMeQuery $query): UserResponse
    {
        return UserResponse::fromUser($this->authenticatedUserProvider->currentUser());
    }
}
