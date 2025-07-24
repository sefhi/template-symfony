<?php

namespace App\Sesame\User\Domain\Security;

use App\Sesame\User\Domain\Entities\User;

interface PasswordHasher
{
    public function hashPlainPassword(User $user, #[\SensitiveParameter]string $plainPassword): string;
}
