<?php

declare(strict_types=1);

namespace LesDatabase\Query\Builder\Applier\Resource;

use Override;
use Doctrine\DBAL\Query\QueryBuilder;

final class InsertResourceApplier extends AbstractResourceApplier
{
    #[Override]
    public function apply(QueryBuilder $builder): QueryBuilder
    {
        $builder->setValue('id', ':id');
        $builder->setParameter('id', $this->id);

        $builder->setValue('activity_last', ':activity_last');
        $builder->setParameter('activity_last', $this->occurredOn);

        return $builder;
    }
}
