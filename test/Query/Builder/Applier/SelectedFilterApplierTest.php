<?php
declare(strict_types=1);

namespace LessDatabaseTest\Query\Builder\Applier;

use Doctrine\DBAL\Query\QueryBuilder;
use LessDatabase\Query\Builder\Applier\SelectedFilterApplier;
use LessValueObject\Collection\CollectionValueObject;
use LessValueObject\Composite\AbstractSelectedFilter;
use LessValueObject\Enum\FilterMode;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessDatabase\Query\Builder\Applier\SelectedFilterApplier
 */
final class SelectedFilterApplierTest extends TestCase
{
    public function testNoneNoSelection(): void
    {
        $filter = $this->getMockForAbstractClass(
            AbstractSelectedFilter::class,
            [
                FilterMode::None,
                null,
            ],
        );

        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->expects(self::once())
            ->method('andWhere')
            ->with('fiz IS NULL');

        $applier = new SelectedFilterApplier('fiz', $filter);
        $applier->apply($builder);
    }

    public function testNoneWithSelection(): void
    {
        $collection = $this->createMock(CollectionValueObject::class);
        $collection
            ->method('current')
            ->willReturnOnConsecutiveCalls(1, 2);

        $collection
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true, true, false);

        $collection
            ->method('count')
            ->willReturn(2);

        $filter = $this->getMockForAbstractClass(
            AbstractSelectedFilter::class,
            [
                FilterMode::None,
                $collection,
            ],
        );

        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->expects(self::once())
            ->method('andWhere')
            ->with('fiz NOT IN (:i_pos_1, :i_pos_2)');

        $builder
            ->expects(self::exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                ['i_pos_1', 1],
                ['i_pos_2', 2],
            );

        $applier = new SelectedFilterApplier('fiz', $filter);
        $applier->apply($builder);
    }

    public function testAnyNoSelection(): void
    {
        $filter = $this->getMockForAbstractClass(
            AbstractSelectedFilter::class,
            [
                FilterMode::Any,
                null,
            ],
        );

        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->expects(self::once())
            ->method('andWhere')
            ->with('fiz IS NOT NULL');

        $applier = new SelectedFilterApplier('fiz', $filter);
        $applier->apply($builder);
    }

    public function testAnyWithSelection(): void
    {
        $collection = $this->createMock(CollectionValueObject::class);
        $collection
            ->method('current')
            ->willReturnOnConsecutiveCalls(1, 2);

        $collection
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true, true, false);

        $collection
            ->method('count')
            ->willReturn(2);

        $filter = $this->getMockForAbstractClass(
            AbstractSelectedFilter::class,
            [
                FilterMode::Any,
                $collection,
            ],
        );

        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->expects(self::once())
            ->method('andWhere')
            ->with('fiz IN (:i_pos_1, :i_pos_2)');

        $builder
            ->expects(self::exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                ['i_pos_1', 1],
                ['i_pos_2', 2],
            );

        $applier = new SelectedFilterApplier('fiz', $filter);
        $applier->apply($builder);
    }

    public function testAllNoSelection(): void
    {
        $filter = $this->getMockForAbstractClass(
            AbstractSelectedFilter::class,
            [
                FilterMode::All,
                null,
            ],
        );

        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->expects(self::never())
            ->method('andWhere');

        $applier = new SelectedFilterApplier('fiz', $filter);
        $applier->apply($builder);
    }

    public function testAllWithSelection(): void
    {
        $collection = $this->createMock(CollectionValueObject::class);
        $collection
            ->method('current')
            ->willReturnOnConsecutiveCalls(1, 2);

        $collection
            ->method('valid')
            ->willReturnOnConsecutiveCalls(true, true, false);

        $collection
            ->method('count')
            ->willReturn(2);

        $filter = $this->getMockForAbstractClass(
            AbstractSelectedFilter::class,
            [
                FilterMode::All,
                $collection,
            ],
        );

        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->expects(self::exactly(2))
            ->method('andWhere')
            ->withConsecutive(
                ['fiz = :i_pos_1'],
                ['fiz = :i_pos_2'],
            );

        $builder
            ->expects(self::exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                ['i_pos_1', 1],
                ['i_pos_2', 2],
            );

        $applier = new SelectedFilterApplier('fiz', $filter);
        $applier->apply($builder);
    }
}
