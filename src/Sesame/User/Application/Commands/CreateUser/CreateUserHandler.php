<?php

declare(strict_types=1);

namespace App\Sesame\User\Application\Commands\CreateUser;

use App\Sesame\User\Domain\Entities\User;
use App\Sesame\User\Domain\Repositories\UserSaveRepository;
use App\Sesame\User\Domain\Security\PasswordHasher;
use App\Shared\Domain\Bus\Command\CommandHandler;

final readonly class CreateUserHandler implements CommandHandler
{
    public function __construct(
        private UserSaveRepository $userRepository,
        private PasswordHasher $passwordHasher,
    ) {
    }

    public function __invoke(CreateUserCommand $command): void
    {
        $user = User::create(
            $command->id,
            $command->name,
            $command->email,
            $command->plainPassword,
            $command->createdAt,
        );

        $hashedPassword = $this->passwordHasher->hashPlainPassword($user, $command->plainPassword);

        $this->userRepository->save($user->withPasswordHashed($hashedPassword));
    }
}
