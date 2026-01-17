<?php

declare(strict_types=1);

namespace App\Template\User\Domain\Repositories;

use App\Template\User\Domain\Entities\User;
use Ramsey\Uuid\UuidInterface;

interface UserFindRepository
{
    public function findById(UuidInterface $id): ?User;

    public function findByEmail(string $email): ?User;
}
