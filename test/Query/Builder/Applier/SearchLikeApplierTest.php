<?php

declare(strict_types=1);

namespace LesDatabaseTest\Query\Builder\Applier;

use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use LesDatabase\Query\Builder\Applier\SearchLikeApplier;
use LesValueObject\String\Format\SearchTerm;
use PHPUnit\Framework\TestCase;

#[CoversClass(\LesDatabase\Query\Builder\Applier\SearchLikeApplier::class)]
final class SearchLikeApplierTest extends TestCase
{
    public function testApply(): void
    {
        $where = '('
            . implode(
                ' OR ',
                [
                    "(fiz like concat('%', :term_full, '%'))",
                    "(foo like concat('%', :term_full, '%'))",
                    "(fiz like concat('%', :term_0, '%'))",
                    "(foo like concat('%', :term_0, '%'))",
                    "(fiz like concat('%', :term_3, '%'))",
                    "(foo like concat('%', :term_3, '%'))",
                ],
            )
            . ')';

        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->expects(self::once())
            ->method('andWhere')
            ->with($where);

        $order = '('
            . implode(
                ' + ',
                [
                    "case when fiz like concat('%', :term_full, '%') then 4 else 0 end",
                    "case when foo like concat('%', :term_full, '%') then 2 else 0 end",
                    "case when fiz like concat('%', :term_0, '%') then 2 else 0 end",
                    "case when foo like concat('%', :term_0, '%') then 1 else 0 end",
                    "case when fiz like concat('%', :term_3, '%') then 2 else 0 end",
                    "case when foo like concat('%', :term_3, '%') then 1 else 0 end",
                ],
            )
            . ')';

        $builder
            ->expects(self::once())
            ->method('addOrderBy')
            ->with($order);

        $applier = new SearchLikeApplier(
            new SearchTerm('& Biz a _ van - Bar %'),
            [
                'fiz' => 2,
                'foo' => 1,
            ],
        );

        $applier->apply($builder);
    }
}
