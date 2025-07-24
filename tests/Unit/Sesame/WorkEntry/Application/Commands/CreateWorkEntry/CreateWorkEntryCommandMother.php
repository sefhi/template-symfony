<?php

declare(strict_types=1);

namespace Tests\Unit\Sesame\WorkEntry\Application\Commands\CreateWorkEntry;

use App\Sesame\WorkEntry\Application\Commands\CreateWorkEntry\CreateWorkEntryCommand;
use Tests\Utils\Mother\MotherCreator;

final class CreateWorkEntryCommandMother
{
    public static function random(array $overrides = []): CreateWorkEntryCommand
    {
        $randomData = [
            'id'        => MotherCreator::id(),
            'userId'    => MotherCreator::id(),
            'startDate' => MotherCreator::dateTime(),
            'createdAt' => MotherCreator::dateTime(),
        ];

        $finalData = array_merge($randomData, $overrides);

        return new CreateWorkEntryCommand(
            id: $finalData['id'],
            userId: $finalData['userId'],
            createdAt: $finalData['createdAt'],
            startDate: $finalData['startDate'],
        );
    }
}
