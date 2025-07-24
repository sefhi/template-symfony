<?php

declare(strict_types=1);

namespace App\Sesame\User\Domain\Exceptions;

use Ramsey\Uuid\UuidInterface;

class UserNotFoundException extends \DomainException
{
    public static function withId(UuidInterface $id): self
    {
        return new self("User with id {$id} not found");
    }
}
