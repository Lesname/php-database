<?php
declare(strict_types=1);

namespace LessDatabaseTest\Factory;

use Doctrine\DBAL\Connection;
use LessDatabase\Factory\ConnectionFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \LessDatabase\Factory\ConnectionFactory
 */
final class ConnectionFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $config = [
            'databases' => [
                Connection::class => [
                    'url' => 'mysql://user:password@localhost/db_name?charset=UTF8',
                ],
            ],
        ];

        $container = $this->createMock(ContainerInterface::class);
        $container
            ->expects(self::once())
            ->method('get')
            ->with('config')
            ->willReturn($config);

        $factory = new ConnectionFactory();
        $connection = $factory($container);

        self::assertInstanceOf(Connection::class, $connection);
    }
}
