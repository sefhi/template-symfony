<?php

declare(strict_types=1);

namespace App\Sesame\User\Application\Commands\UpdateUser;

use App\Shared\Domain\Bus\Command\Command;

/**
 * @see UpdateUserHandler
 */
final readonly class UpdateUserCommand implements Command
{
    public function __construct(
        public string $id,
        public string $name,
        public string $email,
        public \DateTimeImmutable $createdAt,
        public \DateTimeImmutable $updatedAt,
        public ?\DateTimeImmutable $deletedAt,
    ) {
    }
}
