<?php

declare(strict_types=1);

namespace App\Sesame\User\Infrastructure\Persistence\Repositories;

use App\Sesame\User\Domain\Entities\User;
use App\Sesame\User\Domain\Repositories\UserFindRepository;
use App\Shared\Infrastructure\Persistence\Repository\DoctrineRepository;
use Ramsey\Uuid\UuidInterface;

final readonly class DoctrineUserFindRepository extends DoctrineRepository implements UserFindRepository
{
    public function findById(UuidInterface $id): ?User
    {
        /** @var User|null $result */
        $result = $this->repository(User::class)->findOneBy(
            [
                'id' => $id,
            ]
        );

        return $result;
    }

    public function findByEmail(string $email): ?User
    {
        /** @var User|null $result */
        $result = $this->repository(User::class)->findOneBy(
            [
                'email.value' => $email,
            ]
        );

        return $result;
    }
}
