<?php

declare(strict_types=1);

namespace App\Template\User\Domain\Services;

use App\Template\User\Domain\Entities\User;
use App\Template\User\Domain\Exceptions\UserNotFoundException;
use App\Template\User\Domain\Repositories\UserFindRepository;
use Ramsey\Uuid\UuidInterface;

class EnsureExistsUserByIdService
{
    public function __construct(
        private UserFindRepository $userFindRepository,
    ) {
    }

    public function __invoke(UuidInterface $userId): User
    {
        $user = $this->userFindRepository->findById($userId);

        if (null === $user) {
            throw UserNotFoundException::withId($userId);
        }

        return $user;
    }
}
