<?php

declare(strict_types=1);

namespace App\Sesame\WorkEntry\Domain\Exceptions;

use Ramsey\Uuid\UuidInterface;

final class WorkEntryNotFoundException extends \DomainException
{
    public static function withId(UuidInterface $id): self
    {
        return new self(sprintf('Work entry with id %s not found.', $id->toString()));
    }
}
