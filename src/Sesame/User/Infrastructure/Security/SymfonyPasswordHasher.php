<?php

declare(strict_types=1);

namespace App\Sesame\User\Infrastructure\Security;

use App\Sesame\User\Domain\Entities\User;
use App\Sesame\User\Domain\Security\PasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final readonly class SymfonyPasswordHasher implements PasswordHasher
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function hashPlainPassword(User $user, #[\SensitiveParameter] string $plainPassword): string
    {
        $userAdapter = new UserAdapter($user);

        return $this->passwordHasher->hashPassword($userAdapter, $plainPassword);
    }
}
