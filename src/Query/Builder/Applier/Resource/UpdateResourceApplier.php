<?php

declare(strict_types=1);

namespace LesDatabase\Query\Builder\Applier\Resource;

use Override;
use Doctrine\DBAL\Query\QueryBuilder;
use LesDatabase\Query\Builder\Applier\Applier;
use LesDomain\Event\AbstractAggregateEvent;
use LesValueObject\Number\Int\Date\MilliTimestamp;
use LesValueObject\String\Format\Resource\Identifier;

final class UpdateResourceApplier implements Applier
{
    /**
     * @psalm-pure
     */
    public function __construct(
        private readonly Identifier $id,
        private readonly MilliTimestamp $occurredOn,
    ) {}

    /**
     * @psalm-pure
     */
    public static function fromEvent(AbstractAggregateEvent $event): self
    {
        return new self($event->id, $event->occurredOn);
    }

    /**
     * @psalm-impure
     */
    #[Override]
    public function apply(QueryBuilder $builder): QueryBuilder
    {
        $builder->set('version', 'version + 1');

        $builder->set('activity_last', ':activity_last');
        $builder->setParameter('activity_last', $this->occurredOn);

        $builder->andWhere('id = :id');
        $builder->setParameter('id', $this->id);

        return $builder;
    }
}
