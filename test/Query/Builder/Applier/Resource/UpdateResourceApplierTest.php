<?php
declare(strict_types=1);

namespace LesDatabaseTest\Query\Builder\Applier\Resource;

use Override;
use LesDomain\Event\Property\Action;
use LesDomain\Event\Property\Target;
use Doctrine\DBAL\Query\QueryBuilder;
use LesDatabase\Query\Builder\Applier\Resource\UpdateResourceApplier;
use LesDomain\Event\AbstractAggregateEvent;
use LesDomain\Event\Property\Headers;
use LesValueObject\Number\Int\Date\MilliTimestamp;
use LesValueObject\String\Format\Resource\Identifier;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LesDatabase\Query\Builder\Applier\Resource\UpdateResourceApplier
 */
final class UpdateResourceApplierTest extends TestCase
{
    public function testApply(): void
    {
        $id = new Identifier('3d46827e-41f7-4ba4-bfa0-bf3380cdc797');
        $on = MilliTimestamp::now();
        $headers = new Headers();

        $event = new class ($id, $on, $headers) extends AbstractAggregateEvent {
            // phpcs:ignore
            public Target $target {
                get {
                    // phpcs:ignore
                    return $this->target;
                }
            }
            // phpcs:ignore
            public Action $action {
                get {
                    // phpcs:ignore
                    return $this->action;
                }
            }

            #[Override]
            public function getTarget(): Target
            {
                return new Action('foo');
            }

            #[Override]
            public function getAction(): Action
            {
                return new Action('bar');
            }
        };

        $applier = UpdateResourceApplier::fromEvent($event);

        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->expects(self::exactly(2))
            ->method('set');

        $builder
            ->expects(self::once())
            ->method('andWhere')
            ->with('id = :id');
        $builder
            ->expects(self::exactly(2))
            ->method('setParameter');

        $applier->apply($builder);
    }
}
