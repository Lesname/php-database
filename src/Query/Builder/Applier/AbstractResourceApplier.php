<?php
declare(strict_types=1);

namespace LessDatabase\Query\Builder\Applier;

use Doctrine\DBAL\Query\QueryBuilder;

abstract class AbstractResourceApplier implements Applier
{
    /**
     * @return array<string, string|array<string, string|array<string, string|array<string, string|array<string, string|array<string, string|array<string, string|array<string, string>>>>>>>>
     */
    abstract protected function getFields(): array;

    abstract protected function getTableName(): string;

    abstract protected function getTableAlias(): string;

    /**
     * @psalm-suppress MixedArgumentTypeCoercion flatten ...
     */
    public function apply(QueryBuilder $builder): QueryBuilder
    {
        $builder->from("`{$this->getTableName()}`", $this->getTableAlias());

        $applier = SelectApplier::fromNested($this->getFields());
        $applier->apply($builder);

        return $builder;
    }
}
