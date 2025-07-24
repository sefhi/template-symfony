<?php

declare(strict_types=1);

namespace Tests\Utils\Factory\WorkEntry;

use App\Sesame\WorkEntry\Domain\Entities\WorkEntry;
use App\Shared\Domain\Aggregate\AggregateRoot;
use Tests\Unit\Sesame\WorkEntry\Domain\Entities\WorkEntryMother;
use Tests\Utils\Factory\AbstractFactory;

/**
 * @extends AbstractFactory<WorkEntry>
 */
final class WorkEntryFactory extends AbstractFactory
{
    /**
     * @throws \Exception
     */
    public function createOne(AggregateRoot $entity): void
    {
        if (false === $entity instanceof WorkEntry) {
            throw new \Exception('Not implemented');
        }

        $this->persistence->persist($entity);
    }

    public function createMany(int $count = 5): void
    {
        for ($i = 0; $i < $count; ++$i) {
            $this->createOne(WorkEntryMother::random());
        }
    }
}
