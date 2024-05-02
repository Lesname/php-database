<?php
declare(strict_types=1);

namespace LessDatabaseTest\Query\Builder\Applier;

use Doctrine\DBAL\Query\QueryBuilder;
use LessDatabase\Query\Builder\Applier\Applier;
use LessDatabase\Query\Builder\Applier\ChainApplier;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessDatabase\Query\Builder\Applier\ChainApplier
 */
class ChainApplierTest extends TestCase
{
    public function testChain(): void
    {
        $firstBuilder = $this->createMock(QueryBuilder::class);
        $secondBuilder = $this->createMock(QueryBuilder::class);
        $thirdBuilder = $this->createMock(QueryBuilder::class);

        $firstApplier = $this->createMock(Applier::class);
        $firstApplier->expects(self::once())->method('apply')->with($firstBuilder)->willReturn($secondBuilder);

        $secondApplier = $this->createMock(Applier::class);
        $secondApplier->expects(self::once())->method('apply')->with($secondBuilder)->willReturn($thirdBuilder);

        $chainApplier = ChainApplier::chain($firstApplier, $secondApplier);

        self::assertSame($thirdBuilder, $chainApplier->apply($firstBuilder));
    }
}
