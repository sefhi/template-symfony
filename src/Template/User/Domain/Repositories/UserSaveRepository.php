<?php

declare(strict_types=1);

namespace App\Template\User\Domain\Repositories;

use App\Template\User\Domain\Entities\User;

interface UserSaveRepository
{
    public function save(User $user): void;
}
