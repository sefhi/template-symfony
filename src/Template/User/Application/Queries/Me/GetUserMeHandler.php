<?php

declare(strict_types=1);

namespace App\Template\User\Application\Queries\Me;

use App\Shared\Domain\Bus\Query\QueryHandler;
use App\Template\User\Application\Queries\FindUserById\UserResponse;
use App\Template\User\Domain\Security\AuthenticatedUserProvider;

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
