<?php

namespace App\Sesame\User\Domain\Security;

use App\Sesame\User\Domain\Entities\User;

interface AuthenticatedUserProvider
{
    public function currentUser(): User;
}
