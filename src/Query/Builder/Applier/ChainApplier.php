<?php

declare(strict_types=1);

namespace LesDatabase\Query\Builder\Applier;

use Override;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * @psalm-mutable
 */
final class ChainApplier implements Applier
{
    /**
     * @param array<Applier> $appliers
     *
     * @psalm-pure
     */
    public function __construct(private readonly array $appliers)
    {}

    /**
     * @psalm-pure
     */
    public static function chain(Applier ...$appliers): self
    {
        return new self($appliers);
    }

    /**
     * @psalm-impure
     */
    #[Override]
    public function apply(QueryBuilder $builder): QueryBuilder
    {
        foreach ($this->appliers as $applier) {
            $builder = $applier->apply($builder);
        }

        return $builder;
    }
}
