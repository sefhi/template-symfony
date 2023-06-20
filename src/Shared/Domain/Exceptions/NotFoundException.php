<?php

declare(strict_types=1);

namespace App\Shared\Domain\Exceptions;

final class NotFoundException extends \RuntimeException
{
    public static function entityWithId(string $entityClass, string $id): self
    {
        return new self(
            sprintf(
                'Not Found Entity <%s> with id <%s>',
                $entityClass,
                $id
            )
        );
    }
}
