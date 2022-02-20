<?php
declare(strict_types=1);

namespace LessDatabaseTest\Query\Builder\Applier;

use Doctrine\DBAL\Query\QueryBuilder;
use LessDatabase\Query\Builder\Applier\SelectApplier;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessDatabase\Query\Builder\Applier\SelectApplier
 */
final class SelectApplierTest extends TestCase
{
    public function testApply(): void
    {
        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->expects(self::once())
            ->method('addSelect')
            ->with(
                "foo as 'fiz'",
                "yxz as 'bar.biz'",
            )
            ->willReturn($builder);

        $applier = SelectApplier::fromNested(
            [
                'fiz' => 'foo',
                'bar' => [
                    'biz' => 'yxz',
                ],
            ],
        );

        $applier->apply($builder);
    }
}
