<?php

declare(strict_types=1);

namespace App\Sesame\User\Domain\Repositories;

use App\Sesame\User\Domain\Entities\User;

interface UserSaveRepository
{
    public function save(User $user): void;
}
