<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Application\Commands\DeleteWorkEntry;

use App\Shared\Domain\Bus\Command\Command;

/**
 * @see DeleteWorkEntryHandler
 */
final readonly class DeleteWorkEntryCommand implements Command
{
    public function __construct(
        public string $id,
    ) {
    }
}
