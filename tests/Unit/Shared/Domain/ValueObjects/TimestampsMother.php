<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Domain\ValueObject;

use App\Shared\Domain\ValueObject\Timestamps;

class TimestampsMother
{
    public static function create(
        ?\DateTimeImmutable $createdAt = null,
        ?\DateTimeImmutable $updatedAt = null,
    ): Timestamps {
        return Timestamps::create(
            $createdAt ?? (new \DateTimeImmutable(date(\DateTimeInterface::ATOM)))->setTimezone(new \DateTimeZone('UTC')),
            $updatedAt,
        );
    }

    public static function defaultNow(): Timestamps
    {
        return self::create();
    }
}
