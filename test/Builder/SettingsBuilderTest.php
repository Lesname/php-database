<?php
declare(strict_types=1);

namespace LesDatabaseTest\Builder;

use LesDatabase\Builder\SettingsBuilder;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LesDatabase\Builder\SettingsBuilder
 */
class SettingsBuilderTest extends TestCase
{
    public function testWith(): void
    {
        $builder = new SettingsBuilder();
        $setBuilder = $builder
            ->withDriver('driver')
            ->withHost('host')
            ->withUser('user')
            ->withPassword('password')
            ->withCharset('charset');

        self::assertSame([], $builder->build());
        self::assertSame(
            [
                'driver' => 'driver',
                'host' => 'host',
                'user' => 'user',
                'password' => 'password',
                'charset' => 'charset',
            ],
            $setBuilder->build(),
        );
    }
}
