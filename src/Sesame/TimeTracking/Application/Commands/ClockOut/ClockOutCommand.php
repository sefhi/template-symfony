<?php

declare(strict_types=1);

namespace App\Sesame\TimeTracking\Application\Commands\ClockOut;

use App\Shared\Domain\Bus\Command\Command;

/**
 * @see ClockOutHandler
 */
final readonly class ClockOutCommand implements Command
{
    public function __construct(
        public string $workEntryId,
        public string $userId,
        public \DateTimeImmutable $endDate,
    ) {
    }
}
