<?php

declare(strict_types=1);

namespace LesDatabase\Query\Builder\Applier\Values;

use Override;
use Doctrine\DBAL\Query\QueryBuilder;

final class InsertValuesApplier extends AbstractValuesApplier
{
    /**
     * @psalm-impure
     */
    #[Override]
    public function apply(QueryBuilder $builder): QueryBuilder
    {
        foreach ($this->getProccessableKeys($builder) as $field => $key) {
            $builder->setValue(
                sprintf('"%s"', $field),
                $key,
            );
        }

        return $builder;
    }
}
