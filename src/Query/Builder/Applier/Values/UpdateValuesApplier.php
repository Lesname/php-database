<?php
declare(strict_types=1);

namespace LessDatabase\Query\Builder\Applier\Values;

use Doctrine\DBAL\Query\QueryBuilder;

final class UpdateValuesApplier extends AbstractValuesApplier
{
    public function apply(QueryBuilder $builder): QueryBuilder
    {
        foreach ($this->getProccessableKeys($builder) as $field => $key) {
            $builder->set($field, ":{$key}");
        }

        return $builder;
    }
}
