<?php
declare(strict_types=1);

namespace LessDatabase\Query\Builder\Applier;

use Doctrine\DBAL\Query\QueryBuilder;

interface Applier
{
    public function apply(QueryBuilder $builder): QueryBuilder;
}
