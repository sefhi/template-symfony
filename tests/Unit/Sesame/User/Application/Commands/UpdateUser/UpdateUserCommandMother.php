<?php

declare(strict_types=1);

namespace Tests\Unit\Sesame\User\Application\Commands\UpdateUser;

use App\Sesame\User\Application\Commands\UpdateUser\UpdateUserCommand;
use Tests\Utils\Mother\MotherCreator;

final class UpdateUserCommandMother
{
    public static function random(array $overrides = []): UpdateUserCommand
    {
        $createdAt = MotherCreator::dateTime();
        $updatedAt = $createdAt->modify('+8 hours');
        $deletedAt = $updatedAt->modify('+12 hours');

        $randomData = [
            'id'        => MotherCreator::id(),
            'name'      => MotherCreator::name(),
            'email'     => MotherCreator::email(),
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
            'deletedAt' => $deletedAt,
        ];

        $finalData = array_merge($randomData, $overrides);

        return new UpdateUserCommand(
            id: $finalData['id'],
            name: $finalData['name'],
            email: $finalData['email'],
            createdAt: $finalData['createdAt'],
            updatedAt: $finalData['updatedAt'],
            deletedAt: $finalData['deletedAt'],
        );
    }
}
