<?php
declare(strict_types=1);

namespace LessDatabase\Query\Builder\Applier\Resource;

use Doctrine\DBAL\Query\QueryBuilder;
use LessDatabase\Query\Builder\Applier\Applier;
use LessDomain\Event\AbstractAggregateEvent;
use LessValueObject\Number\Int\Date\MilliTimestamp;
use LessValueObject\String\Format\Resource\Identifier;

final class UpdateResourceApplier implements Applier
{
    public function __construct(
        private readonly Identifier $id,
        private readonly MilliTimestamp $occurredOn,
    ) {}

    public static function fromEvent(AbstractAggregateEvent $event): self
    {
        return new self($event->id, $event->getOccuredOn());
    }

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
