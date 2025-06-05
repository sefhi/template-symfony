<?php

declare(strict_types=1);

namespace App\Sesame\User\Application\Commands\CreateUser;

use App\Shared\Domain\Bus\Command\Command;

final readonly class CreateUserCommand implements Command
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public string $password,
        public \DateTimeImmutable $createdAt,
    ) {
    }
}
