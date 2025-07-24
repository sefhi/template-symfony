<?php

declare(strict_types=1);

namespace Tests\Unit\Sesame\WorkEntry\Application\Commands\UpdateWorkEntry;

use App\Sesame\WorkEntry\Application\Commands\UpdateWorkEntry\UpdateWorkEntryCommand;
use Tests\Utils\Mother\MotherCreator;

final class UpdateWorkEntryCommandMother
{
    public static function random(array $overrides = []): UpdateWorkEntryCommand
    {
        $createdAt = MotherCreator::dateTime();
        $startDate = $createdAt;
        $updatedAt = $createdAt->modify('+8 hours');
        $endDate   = $updatedAt->modify('+12 hours');

        $randomData = [
            'id'        => MotherCreator::id(),
            'userId'    => MotherCreator::id(),
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
            'deletedAt' => null,
        ];

        $finalData = array_merge($randomData, $overrides);

        return new UpdateWorkEntryCommand(
            id: $finalData['id'],
            userId: $finalData['userId'],
            startDate: $finalData['startDate'],
            endDate: $finalData['endDate'],
            createdAt: $finalData['createdAt'],
            updatedAt: $finalData['updatedAt'],
            deletedAt: $finalData['deletedAt'],
        );
    }
}
