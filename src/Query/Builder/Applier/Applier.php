<?php
declare(strict_types=1);

namespace LesDatabase\Query\Builder\Applier;

use Doctrine\DBAL\Query\QueryBuilder;

interface Applier
{
    public function apply(QueryBuilder $builder): QueryBuilder;
}
