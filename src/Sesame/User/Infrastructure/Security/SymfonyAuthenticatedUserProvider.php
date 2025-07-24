<?php

declare(strict_types=1);

namespace App\Sesame\User\Infrastructure\Security;

use App\Sesame\User\Domain\Entities\User;
use App\Sesame\User\Domain\Security\AuthenticatedUserProvider;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final readonly class SymfonyAuthenticatedUserProvider implements AuthenticatedUserProvider
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
    ) {
    }

    public function currentUser(): User
    {
        $token = $this->tokenStorage->getToken();

        if (!$token) {
            throw new UnauthorizedHttpException('Bearer', 'No authentication token found');
        }

        /** @var UserAdapter|null $userAdapter */
        $userAdapter = $token->getUser();

        if (!$userAdapter instanceof UserAdapter) {
            throw new UnauthorizedHttpException('Bearer', 'Invalid user type');
        }

        return $userAdapter->getUser();
    }
}
