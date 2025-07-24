<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Persistence\Doctrine\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

final class DeletedAtFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, string $targetTableAlias): string
    {
        if ($targetEntity->hasField('timestamps.deletedAt')) {
            return $targetTableAlias . '.deleted_at IS NULL';
        }

        return '';
    }
}
