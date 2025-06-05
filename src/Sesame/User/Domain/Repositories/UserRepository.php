<?php

declare(strict_types=1);

namespace App\Sesame\User\Domain\Repositories;

use App\Sesame\User\Domain\Entities\User;

interface UserRepository
{
    public function save(User $user): void;
}
