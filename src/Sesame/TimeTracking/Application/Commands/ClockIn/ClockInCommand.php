<?php

declare(strict_types=1);

namespace App\Sesame\TimeTracking\Application\Commands\ClockIn;

use App\Shared\Domain\Bus\Command\Command;

/**
 * @see ClockInHandler
 */
final readonly class ClockInCommand implements Command
{
    public function __construct(
        public string $workEntryId,
        public string $userId,
        public \DateTimeImmutable $startDate,
    ) {
    }
}
