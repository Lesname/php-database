<?php

declare(strict_types=1);

namespace LesDatabaseTest\Query\Builder\Applier;

use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use LesDatabase\Query\Builder\Applier\Applier;
use LesDatabase\Query\Builder\Applier\ChainApplier;
use PHPUnit\Framework\TestCase;

#[CoversClass(\LesDatabase\Query\Builder\Applier\ChainApplier::class)]
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
