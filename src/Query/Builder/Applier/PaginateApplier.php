<?php

declare(strict_types=1);

namespace LesDatabase\Query\Builder\Applier;

use Override;
use Doctrine\DBAL\Query\QueryBuilder;
use LesValueObject\Composite\Paginate;

final class PaginateApplier implements Applier
{
    /**
     * @psalm-mutation-free
     */
    public function __construct(private ?Paginate $paginate)
    {}

    /**
     * @psalm-impure
     */
    #[Override]
    public function apply(QueryBuilder $builder): QueryBuilder
    {
        if ($this->paginate) {
            $builder->setFirstResult($this->paginate->getSkipped());
            $builder->setMaxResults($this->paginate->perPage->value);
        }

        return $builder;
    }
}
