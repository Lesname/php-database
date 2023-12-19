<?php
declare(strict_types=1);

namespace LessDatabaseTest\Query\Builder\Applier\Values;

use Doctrine\DBAL\Query\QueryBuilder;
use LessDatabase\Query\Builder\Applier\Values\UpdateValuesApplier;
use LessValueObject\Number\Int\IntValueObject;
use LessValueObject\String\StringValueObject;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LessDatabase\Query\Builder\Applier\Values\UpdateValuesApplier
 */
final class UpdateValuesApplierTest extends TestCase
{
    public function testApply(): void
    {
        $string = $this->createMock(StringValueObject::class);
        $string->method('__toString')->willReturn('string');

        $number = $this->createMock(IntValueObject::class);
        $number->method('getValue')->willReturn(3);

        $values = [
            'foo' => $string,
            'bar' => $number,
        ];

        $applier = new UpdateValuesApplier($values);

        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->expects(self::exactly(2))
            ->method('setParameter');

        $builder
            ->expects(self::exactly(2))
            ->method('set');

        $applier->apply($builder);
    }
}
