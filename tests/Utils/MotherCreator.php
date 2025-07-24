<?php

declare(strict_types=1);

namespace Tests\Utils;

use Faker\Factory;
use Faker\Generator;

/**
 * @deprecated use Tests\Utils\Mother\MotherCreator
 */
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

    public static function isbn(): string
    {
        return 0 === random_int(0, 1)
            ? self::random()->isbn10()
            : self::random()->isbn13();
    }

    public static function title(): string
    {
        return self::random()->sentence(3);
    }

    public static function author(): string
    {
        return self::random()->name();
    }

    public static function description(): string
    {
        return self::random()->text();
    }

    public static function stock(): int
    {
        return self::random()->numberBetween(1, 100);
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
        return self::random()->password();
    }

    public static function dateTime(): \DateTimeImmutable
    {
        return \DateTimeImmutable::createFromMutable(self::random()->dateTime());
    }
}
