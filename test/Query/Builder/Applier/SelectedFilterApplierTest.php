<?php
declare(strict_types=1);

namespace LesDatabaseTest\Query\Builder\Applier;

use Doctrine\DBAL\Query\QueryBuilder;
use LesDatabase\Query\Builder\Applier\SelectedFilterApplier;
use LesValueObject\Collection\CollectionValueObject;
use LesValueObject\Composite\AbstractSelectedFilter;
use LesValueObject\Enum\FilterMode;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LesDatabase\Query\Builder\Applier\SelectedFilterApplier
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

        $filter = new class (FilterMode::None, $collection) extends AbstractSelectedFilter {
        };

        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->method('createNamedParameter')
            ->willReturnOnConsecutiveCalls(
                ':i_pos_1',
                ':i_pos_2',
            );

        $builder
            ->expects(self::once())
            ->method('andWhere')
            ->with('fiz NOT IN (:i_pos_1, :i_pos_2)');

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

        $filter = new class (FilterMode::Any, $collection) extends AbstractSelectedFilter {
        };

        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->method('createNamedParameter')
            ->willReturnOnConsecutiveCalls(
                ':i_pos_1',
                ':i_pos_2',
            );

        $builder
            ->expects(self::once())
            ->method('andWhere')
            ->with('fiz IN (:i_pos_1, :i_pos_2)');

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

        $filter = new class (FilterMode::All, $collection) extends AbstractSelectedFilter {
        };

        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->expects(self::exactly(2))
            ->method('andWhere');

        $applier = new SelectedFilterApplier('fiz', $filter);
        $applier->apply($builder);
    }
}
