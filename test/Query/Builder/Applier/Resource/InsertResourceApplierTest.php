<?php

declare(strict_types=1);

namespace LesDatabaseTest\Query\Builder\Applier\Resource;

use LesDomain\Event\Property\Action;
use LesDomain\Event\Property\Target;
use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use LesDomain\Event\AbstractAggregateEvent;
use LesDomain\Event\Property\Headers;
use LesValueObject\Number\Int\Date\MilliTimestamp;
use LesValueObject\String\Format\Resource\Identifier;
use PHPUnit\Framework\TestCase;
use LesDatabase\Query\Builder\Applier\Resource\InsertResourceApplier;

#[CoversClass(InsertResourceApplier::class)]
final class InsertResourceApplierTest extends TestCase
{
    public function testApply(): void
    {
        $id = new Identifier('3d46827e-41f7-4ba4-bfa0-bf3380cdc797');
        $on = new MilliTimestamp(123);
        $headers = new Headers();

        $event = new class ($id, $on, $headers) extends AbstractAggregateEvent {
            // phpcs:ignore
            public Target $target {
                get {
                    // phpcs:ignore
                    return new Target('foo');
                }
            }
            // phpcs:ignore
            public Action $action {
                get {
                    // phpcs:ignore
                    return new Action('bar');
                }
            }
        };

        $applier = InsertResourceApplier::fromEvent($event);

        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->expects(self::exactly(2))
            ->method('setValue')
            ->withParameterSetsInAnyOrder(
                ['id', ':id'],
                ['activity_last', ':activity_last'],
            );

        $builder
            ->expects(self::exactly(2))
            ->method('setParameter')
            ->withParameterSetsInAnyOrder(
                ['id', $id],
                ['activity_last', $on],
            );

        $applier->apply($builder);
    }
}
