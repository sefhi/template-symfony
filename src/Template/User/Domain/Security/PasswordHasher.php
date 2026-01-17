<?php

namespace App\Template\User\Domain\Security;

use App\Template\User\Domain\Entities\User;

interface PasswordHasher
{
    public function hashPlainPassword(User $user, #[\SensitiveParameter]string $plainPassword): string;
}
