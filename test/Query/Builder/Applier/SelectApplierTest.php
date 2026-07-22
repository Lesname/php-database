<?php

declare(strict_types=1);

namespace LesDatabaseTest\Query\Builder\Applier;

use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use LesDatabase\Query\Builder\Applier\SelectApplier;
use PHPUnit\Framework\TestCase;

#[CoversClass(\LesDatabase\Query\Builder\Applier\SelectApplier::class)]
final class SelectApplierTest extends TestCase
{
    public function testApply(): void
    {
        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->expects(self::once())
            ->method('addSelect')
            ->with(
                'foo as "fiz"',
                'yxz as "bar.biz"',
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
