<?php

declare(strict_types=1);

namespace Tests\Unit\Shared\Domain\ValueObjects;

use App\Shared\Domain\ValueObjects\Timestamps;

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
