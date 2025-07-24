<?php

declare(strict_types=1);

namespace App\Sesame\TimeTracking\Infrastructure\Api\ClockOut;

use App\Sesame\TimeTracking\Application\Commands\ClockOut\ClockOutCommand;
use Symfony\Component\Validator\Constraints as Assert;

final class ClockOutRequest
{
    public function __construct(
        #[Assert\DateTime(format: \DateTimeInterface::ATOM, message: '<endDate> must be a valid date time in the format ' . \DateTimeInterface::ATOM)]
        public ?string $endDate = null,
    ) {
    }

    public function toClockOutCommand(
        string $workEntryId,
        string $userId,
    ): ClockOutCommand {
        return new ClockOutCommand(
            $workEntryId,
            $userId,
            $this->endDate
                ? new \DateTimeImmutable($this->endDate)
                : new \DateTimeImmutable(),
        );
    }
}
