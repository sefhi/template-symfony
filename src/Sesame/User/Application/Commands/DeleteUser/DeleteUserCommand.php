<?php

declare(strict_types=1);

namespace App\Sesame\User\Application\Commands\DeleteUser;

use App\Shared\Domain\Bus\Command\Command;

/**
 * @see DeleteUserHandler
 */
final readonly class DeleteUserCommand implements Command
{
    public function __construct(
        public string $id,
    ) {
    }
}
