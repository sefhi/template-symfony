<?php

declare(strict_types=1);

namespace App\Sesame\User\Domain\Repositories;

use App\Sesame\User\Domain\Entities\User;
use Ramsey\Uuid\UuidInterface;

interface UserFindRepository
{
    public function findById(UuidInterface $id): ?User;

    public function findByEmail(string $email): ?User;
}
