<?php
declare(strict_types=1);

namespace LessDatabaseTest\Query\Builder\Applier\Values;

use Doctrine\DBAL\Query\QueryBuilder;
use LessDatabase\Query\Builder\Applier\Values\AbstractValuesApplier;
use LessValueObject\Number\Int\IntValueObject;
use LessValueObject\String\StringValueObject;
use PHPUnit\Framework\TestCase;
use Traversable;

/**
 * @covers \LessDatabase\Query\Builder\Applier\Values\AbstractValuesApplier
 */
final class AbstractValuesApplierTest extends TestCase
{
    public function testProcessKey(): void
    {
        $string = $this->createMock(StringValueObject::class);
        $string->method('__toString')->willReturn('string');

        $number = $this->createMock(IntValueObject::class);
        $number->method('getValue')->willReturn(3);

        $values = [
            'foo' => $string,
            'bar' => $number,
        ];

        $class = new class ($values) extends AbstractValuesApplier {
            public function apply(QueryBuilder $builder): QueryBuilder
            {
                return $builder;
            }

            public function getProccessableKeys(QueryBuilder $builder): iterable
            {
                yield from parent::getProccessableKeys($builder);
            }
        };


        $builder = $this->createMock(QueryBuilder::class);
        $builder
            ->expects(self::exactly(2))
            ->method('setParameter')
            ->withConsecutive(
                ['sf_b45cffe084dd3d20d928bee85e7b0f21', 'string'],
                ['i_pos_3', 3],
            );

        $processed = $class->getProccessableKeys($builder);
        $processed = $processed instanceof Traversable
            ? iterator_to_array($processed)
            : $processed;

        self::assertSame(
            [
                'foo' => 'sf_b45cffe084dd3d20d928bee85e7b0f21',
                'bar' => 'i_pos_3',
            ],
            $processed,
        );
    }
}
