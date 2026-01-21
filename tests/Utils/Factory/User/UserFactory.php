<?php

declare(strict_types=1);

namespace Tests\Utils\Factory\User;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Template\User\Domain\Entities\User;
use Tests\Unit\Template\User\Domain\Entities\UserMother;
use Tests\Utils\Factory\AbstractFactory;

/**
 * @extends AbstractFactory<User>
 */
final class UserFactory extends AbstractFactory
{
    /**
     * @throws \Exception
     */
    public function createOne(AggregateRoot $entity): void
    {
        if (false === $entity instanceof User) {
            throw new \Exception('Not implemented');
        }

        $this->persistence->persist($entity);
    }

    public function createMany(int $count = 5): void
    {
        for ($i = 0; $i < $count; ++$i) {
            $this->createOne(UserMother::random());
        }
    }
}
