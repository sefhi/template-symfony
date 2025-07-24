<?php

declare(strict_types=1);

namespace App\Sesame\User\Infrastructure\Security;

use App\Sesame\User\Domain\Entities\User;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

final readonly class UserAdapter implements UserInterface, PasswordAuthenticatedUserInterface
{
    public function __construct(
        private User $user,
    ) {
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getRoles(): array
    {
        return ['ROLE_USER'];
    }

    public function eraseCredentials(): void
    {
        // TODO implement
    }

    /**
     * @return non-empty-string
     */
    public function getUserIdentifier(): string
    {
        $email = $this->user->emailValue();

        if (empty($email)) {
            throw new \RuntimeException('email cannot be empty');
        }

        return $email;
    }

    public function getPassword(): string
    {
        return $this->user->passwordValue();
    }
}
