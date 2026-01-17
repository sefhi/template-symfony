<?php

namespace App\Template\User\Domain\Security;

use App\Template\User\Domain\Entities\User;

interface AuthenticatedUserProvider
{
    public function currentUser(): User;
}
