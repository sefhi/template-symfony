<?php

declare(strict_types=1);

namespace App\Sesame\TimeTracking\Domain\Exceptions;

use Ramsey\Uuid\UuidInterface;

final class WorkEntryNotBelongToUserException extends \DomainException
{
    public static function withId(UuidInterface $workEntryId): self
    {
        return new self(sprintf('Work entry with id %s does not belong to user', $workEntryId));
    }
}
