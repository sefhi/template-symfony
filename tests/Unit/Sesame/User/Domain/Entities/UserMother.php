<?php

declare(strict_types=1);

namespace Tests\Unit\Sesame\User\Domain\Entities;

use App\Sesame\User\Application\Commands\CreateUser\CreateUserCommand;
use App\Sesame\User\Domain\Entities\User;
use Tests\Utils\Mother\MotherCreator;

final class UserMother
{
    public static function random(array $overrides = []): User
    {
        $createdAt = MotherCreator::dateTime();
        $updatedAt = $createdAt->modify('+8 hours');

        $randomData = [
            'id'        => MotherCreator::id(),
            'name'      => MotherCreator::name(),
            'email'     => MotherCreator::email(),
            'password'  => MotherCreator::password(),
            'createdAt' => $createdAt,
            'updatedAt' => $updatedAt,
            'deletedAt' => null,
        ];

        $finalData = array_merge($randomData, $overrides);

        return User::make(
            id: $finalData['id'],
            name: $finalData['name'],
            email: $finalData['email'],
            password: $finalData['password'],
            createdAt: $finalData['createdAt'],
            updatedAt: $finalData['updatedAt'],
            deletedAt: $finalData['deletedAt'],
        );
    }

    public static function create(array $overrides = []): User
    {
        $randomData = [
            'id'        => MotherCreator::id(),
            'name'      => MotherCreator::name(),
            'email'     => MotherCreator::email(),
            'password'  => MotherCreator::password(),
            'createdAt' => MotherCreator::dateTime(),
            'updatedAt' => null,
        ];

        $finalData = array_merge($randomData, $overrides);

        return User::create(
            id: $finalData['id'],
            name: $finalData['name'],
            email: $finalData['email'],
            password: $finalData['password'],
            createdAt: $finalData['createdAt'],
        );
    }

    public static function admin(): User
    {
        return self::create(
            [
                'name'     => 'admin',
                'email'    => 'admin@app.es',
                'password' => 't@hi$si_smypAs5w0rD',
            ]
        );
    }

    public static function fromCreateUserCommand(CreateUserCommand $command): User
    {
        return self::create(
            [
                'id'        => $command->id,
                'name'      => $command->name,
                'email'     => $command->email,
                'password'  => $command->plainPassword,
                'createdAt' => $command->createdAt,
            ]
        );
    }
}
