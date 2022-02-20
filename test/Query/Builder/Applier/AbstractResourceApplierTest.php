<?php
declare(strict_types=1);

namespace LessDatabaseTest\Query\Builder\Applier;

use Doctrine\DBAL\Query\QueryBuilder;
use LessDatabase\Query\Builder\Applier\AbstractResourceApplier;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessDatabase\Query\Builder\Applier\AbstractResourceApplier
 */
final class AbstractResourceApplierTest extends TestCase
{
    public function testApply(): void
    {
        $applier = $this->getMockForAbstractClass(AbstractResourceApplier::class);
        $applier
            ->method('getFields')
            ->willReturn(
                [
                    'id' => 'a.id',
                    'type' => 'type',
                ],
            );

        $applier
            ->method('getTableName')
            ->willReturn('name');

        $applier
            ->method('getTableAlias')
            ->willReturn('alias');

        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->expects(self::once())
            ->method('from')
            ->with('`name`', 'alias')
            ->willReturn($builder);

        $builder
            ->expects(self::once())
            ->method('addSelect')
            ->with(
                "a.id as 'id'",
                "type as 'type'",
            )
            ->willReturn($builder);

        $applier->apply($builder);
    }
}
