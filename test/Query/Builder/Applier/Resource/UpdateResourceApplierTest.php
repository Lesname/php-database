<?php
declare(strict_types=1);

namespace LessDatabaseTest\Query\Builder\Applier\Resource;

use Doctrine\DBAL\Query\QueryBuilder;
use LessDatabase\Query\Builder\Applier\Resource\UpdateResourceApplier;
use LessDomain\Event\AbstractAggregateEvent;
use LessDomain\Event\Property\Headers;
use LessValueObject\Number\Int\Date\MilliTimestamp;
use LessValueObject\String\Format\Resource\Identifier;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessDatabase\Query\Builder\Applier\Resource\UpdateResourceApplier
 */
final class UpdateResourceApplierTest extends TestCase
{
    public function testApply(): void
    {
        $id = new Identifier('3d46827e-41f7-4ba4-bfa0-bf3380cdc797');
        $on = MilliTimestamp::now();
        $headers = new Headers();

        $event = $this->getMockForAbstractClass(
            AbstractAggregateEvent::class,
            [$id, $on, $headers],
        );

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
