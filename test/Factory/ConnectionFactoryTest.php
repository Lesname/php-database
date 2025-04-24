<?php
declare(strict_types=1);

namespace LesDatabaseTest\Factory;

use Doctrine\DBAL\Connection;
use LesDatabase\Factory\ConnectionFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

/**
 * @covers \LesDatabase\Factory\ConnectionFactory
 */
final class ConnectionFactoryTest extends TestCase
{
    public function testCreate(): void
    {
        $config = [
            'databases' => [
                Connection::class => [
                    'driver' => 'pdo_mysql',
                    'host' => 'localhost',
                    'user' => 'user',
                    'password' => 'password',
                    'dbname' => 'db_name',
                    'charset' => 'UTF8MB4',
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
        $connection = $factory($container, Connection::class);

        self::assertInstanceOf(Connection::class, $connection);
    }
}
