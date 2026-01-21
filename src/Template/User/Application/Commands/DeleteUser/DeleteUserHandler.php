<?php

declare(strict_types=1);

namespace App\Template\User\Application\Commands\DeleteUser;

use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Template\User\Domain\Exceptions\UserNotFoundException;
use App\Template\User\Domain\Repositories\UserFindRepository;
use App\Template\User\Domain\Repositories\UserSaveRepository;
use Ramsey\Uuid\Uuid;

final readonly class DeleteUserHandler implements CommandHandler
{
    public function __construct(
        private UserSaveRepository $userSaveRepository,
        private UserFindRepository $userFindRepository,
    ) {
    }

    public function __invoke(DeleteUserCommand $command): void
    {
        $userId = Uuid::fromString($command->id);
        $user   = $this->userFindRepository->findById($userId);

        if (null === $user) {
            throw UserNotFoundException::withId($userId);
        }

        $user->delete();
        $this->userSaveRepository->save($user);
    }
}
