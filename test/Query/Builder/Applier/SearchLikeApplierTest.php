<?php
declare(strict_types=1);

namespace LessDatabaseTest\Query\Builder\Applier;

use Doctrine\DBAL\Query\QueryBuilder;
use LessDatabase\Query\Builder\Applier\SearchLikeApplier;
use LessValueObject\String\Format\SearchTerm;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessDatabase\Query\Builder\Applier\SearchLikeApplier
 */
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
                    "if(fiz like concat('%', :term_full, '%'), 4, 0)",
                    "if(foo like concat('%', :term_full, '%'), 2, 0)",
                    "if(fiz like concat('%', :term_0, '%'), 2, 0)",
                    "if(foo like concat('%', :term_0, '%'), 1, 0)",
                    "if(fiz like concat('%', :term_3, '%'), 2, 0)",
                    "if(foo like concat('%', :term_3, '%'), 1, 0)",
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
