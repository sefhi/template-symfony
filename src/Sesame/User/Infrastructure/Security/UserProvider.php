<?php

declare(strict_types=1);

namespace App\Sesame\User\Infrastructure\Security;

use App\Sesame\User\Domain\Repositories\UserFindRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @implements UserProviderInterface<UserAdapter>
 */
final readonly class UserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(
        private UserFindRepository $userFindRepository,
    ) {
    }

    /**
     * @param string $identifier
     *
     * @return UserAdapter
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->userFindRepository->findByEmail($identifier);

        if (null === $user) {
            throw new UserNotFoundException(sprintf('User with email "%s" not found.', $identifier));
        }

        return new UserAdapter($user);
    }

    /**
     * @param UserInterface $user
     *
     * @return UserAdapter
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof UserAdapter) {
            throw new UnsupportedUserException(sprintf('Invalid user class "%s".', get_class($user)));
        }

        $refreshedUser = $this->userFindRepository->findByEmail($user->getUserIdentifier());

        if (null === $refreshedUser) {
            throw new UserNotFoundException(sprintf('User with email "%s" not found.', $user->getUserIdentifier()));
        }

        return new UserAdapter($refreshedUser);
    }

    public function supportsClass(string $class): bool
    {
        return UserAdapter::class === $class || is_subclass_of($class, UserAdapter::class);
    }

    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        // TODO implement
    }
}
