<?php
declare(strict_types=1);

namespace LessDatabase\Query\Builder\Applier;

use Doctrine\DBAL\Query\QueryBuilder;
use LessValueObject\Composite\Paginate;

final class PaginateApplier implements Applier
{
    public function __construct(private ?Paginate $paginate)
    {}

    public function apply(QueryBuilder $builder): QueryBuilder
    {
        if ($this->paginate) {
            $builder->setFirstResult($this->paginate->getSkipped());
            $builder->setMaxResults($this->paginate->perPage->getValue());
        }

        return $builder;
    }
}
