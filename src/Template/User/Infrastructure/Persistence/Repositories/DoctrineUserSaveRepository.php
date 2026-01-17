<?php

declare(strict_types=1);

namespace App\Template\User\Infrastructure\Persistence\Repositories;

use App\Template\User\Domain\Entities\User;
use App\Template\User\Domain\Repositories\UserSaveRepository;
use App\Shared\Infrastructure\Persistence\Repository\DoctrineRepository;

final readonly class DoctrineUserSaveRepository extends DoctrineRepository implements UserSaveRepository
{
    public function save(User $user): void
    {
        $this->persist($user);
    }
}
