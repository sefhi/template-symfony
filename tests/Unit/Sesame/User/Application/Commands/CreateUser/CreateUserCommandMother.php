<?php

declare(strict_types=1);

namespace Tests\Unit\Sesame\User\Application\Commands\CreateUser;

use App\Sesame\User\Application\Commands\CreateUser\CreateUserCommand;
use Tests\Utils\Mother\MotherCreator;

final class CreateUserCommandMother
{
    public static function random(array $overrides = []): CreateUserCommand
    {
        $createdAt = MotherCreator::dateTime();

        $randomData = [
            'id'        => MotherCreator::id(),
            'name'      => MotherCreator::name(),
            'email'     => MotherCreator::email(),
            'password'  => MotherCreator::password(),
            'createdAt' => $createdAt,
        ];

        $finalData = array_merge($randomData, $overrides);

        return new CreateUserCommand(
            id: $finalData['id'],
            name: $finalData['name'],
            email: $finalData['email'],
            plainPassword: $finalData['password'],
            createdAt: $finalData['createdAt'],
        );
    }
}
