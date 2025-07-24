<?php

declare(strict_types=1);

namespace Tests\Utils\Mother;

use Faker\Factory;
use Faker\Generator;

final class MotherCreator
{
    private static ?Generator $faker = null;

    public static function random(): Generator
    {
        return self::$faker = self::$faker ?? Factory::create('es_ES');
    }

    public static function create(): Generator
    {
        return self::random();
    }

    public static function id(): string
    {
        return self::random()->uuid();
    }

    public static function email(): string
    {
        return self::random()->email();
    }

    public static function name(): string
    {
        return self::random()->name();
    }

    public static function password(): string
    {
        return self::random()->password(minLength: 8, maxLength: 16);
    }

    public static function dateTime(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromMutable(self::random()->dateTime());
    }

    public static function dateTimeFormat(string $format = \DateTimeInterface::ATOM): string
    {
        return self::dateTime()->format($format);
    }
}
