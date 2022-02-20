<?php
declare(strict_types=1);

namespace LessDatabaseTest\Query\Builder\Applier;

use Doctrine\DBAL\Query\QueryBuilder;
use LessDatabase\Query\Builder\Applier\PaginateApplier;
use LessValueObject\Composite\Paginate;
use LessValueObject\Number\Int\Paginate\Page;
use LessValueObject\Number\Int\Paginate\PerPage;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessDatabase\Query\Builder\Applier\PaginateApplier
 */
final class PaginateApplierTest extends TestCase
{
    public function testSetup(): void
    {
        $paginate = new Paginate(
            new PerPage(15),
            new Page(3),
        );

        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->expects(self::once())
            ->method('setFirstResult')
            ->with(30);
        $builder
            ->expects(self::once())
            ->method('setMaxResults')
            ->with(15);

        $applier = new PaginateApplier($paginate);
        $applier->apply($builder);
    }
}
