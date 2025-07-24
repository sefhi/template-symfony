<?php

declare(strict_types=1);

namespace App\Sesame\TimeTracking\Infrastructure\Api\ClockIn;

use App\Sesame\TimeTracking\Application\Commands\ClockIn\ClockInCommand;
use Symfony\Component\Validator\Constraints as Assert;

final class ClockInRequest
{
    public function __construct(
        #[Assert\DateTime(format: \DateTimeInterface::ATOM, message: '<startDate> must be a valid date time in the format ' . \DateTimeInterface::ATOM)]
        public ?string $startDate = null,
    ) {
    }

    public function toClockInCommand(
        string $workEntryId,
        string $userId,
    ): ClockInCommand {
        return new ClockInCommand(
            $workEntryId,
            $userId,
            $this->startDate
                ? new \DateTimeImmutable($this->startDate) : new \DateTimeImmutable(),
        );
    }
}
