<?php

declare(strict_types=1);

namespace LesDatabase\Query\Builder\Applier\Resource;

use LesDomain\Event\AbstractAggregateEvent;
use LesDatabase\Query\Builder\Applier\Applier;
use LesValueObject\Number\Int\Date\MilliTimestamp;
use LesValueObject\String\Format\Resource\Identifier;

abstract class AbstractResourceApplier implements Applier
{
    /**
     * @psalm-pure
     */
    final public function __construct(
        protected readonly Identifier $id,
        protected readonly MilliTimestamp $occurredOn,
    ) {}

    /**
     * @psalm-pure
     */
    public static function fromEvent(AbstractAggregateEvent $event): self
    {
        return new static($event->id, $event->occurredOn);
    }
}
