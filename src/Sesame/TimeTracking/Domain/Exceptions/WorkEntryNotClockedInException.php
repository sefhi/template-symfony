<?php

declare(strict_types=1);

namespace App\Sesame\TimeTracking\Domain\Exceptions;

use Ramsey\Uuid\UuidInterface;

final class WorkEntryNotClockedInException extends \DomainException
{
    public static function withId(UuidInterface $id): self
    {
        return new self(sprintf('Work entry with id %s is not clocked in', $id));
    }
}
