<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Application\Commands\CreateWorkEntry;

use App\Shared\Domain\Bus\Command\Command;

/**
 * @see CreateWorkEntryHandler
 */
final readonly class CreateWorkEntryCommand implements Command
{
    public function __construct(
        public string $id,
        public string $userId,
        public \DateTimeImmutable $createdAt,
        public ?\DateTimeImmutable $startDate = null,
    ) {
    }
}
