<?php
declare(strict_types=1);

namespace LessDatabase\Query\Builder\Applier;

use Doctrine\DBAL\Query\QueryBuilder;

final class ChainApplier implements Applier
{
    /**
     * @param array<Applier> $appliers
     */
    public function __construct(private readonly array $appliers)
    {}

    public static function chain(Applier ...$appliers): self
    {
        return new self($appliers);
    }

    public function apply(QueryBuilder $builder): QueryBuilder
    {
        foreach ($this->appliers as $applier) {
            $builder = $applier->apply($builder);
        }

        return $builder;
    }
}
