<?php

declare(strict_types=1);

namespace LesDatabase\Query\Builder\Applier;

use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @psalm-mutable
 */
interface Applier
{
    /**
     * @psalm-impure
     */
    public function apply(QueryBuilder $builder): QueryBuilder;
}
