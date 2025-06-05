<?php

declare(strict_types=1);

namespace Tests\Unit\Sesame\WorkEntry\Domain\Entities;

use App\Sesame\WorkEntry\Domain\Entities\WorkEntry;
use Tests\Utils\MotherCreator;

final class WorkEntryMother
{
    public static function random(array $overrides = []): WorkEntry
    {
        $createdAt  = MotherCreator::dateTime();
        $hourRandom = random_int(0, 23);
        $updatedAt  = $createdAt->modify("+{$hourRandom} hours");

        $randomData = [
            'id'        => MotherCreator::id(),
            'userId'    => MotherCreator::id(),
            'startDate' => $createdAt,
            'endDate'   => $createdAt->modify('+1 hour'),
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
        ];

        $finalData = array_merge($randomData, $overrides);

        return WorkEntry::make(
            id: $finalData['id'],
            userId: $finalData['userId'],
            startDate: $finalData['startDate'],
            createdAt: $finalData['createdAt'],
            endDate: $finalData['endDate'],
            updatedAt: $finalData['updatedAt'],
        );
    }

    public static function start(array $overrides = []): WorkEntry
    {
        $randomData = [
            'id'        => MotherCreator::id(),
            'userId'    => MotherCreator::id(),
            'startDate' => MotherCreator::dateTime(),
            'endDate'   => null,
            'createdAt' => MotherCreator::dateTime(),
            'updatedAt' => null,
        ];

        $finalData = array_merge($randomData, $overrides);

        return WorkEntry::start(
            id: $finalData['id'],
            userId: $finalData['userId'],
            startDate: $finalData['startDate'],
            createdAt: $finalData['createdAt'],
        );
    }
}
