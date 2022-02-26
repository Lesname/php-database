<?php
declare(strict_types=1);

namespace LessDatabase\Factory;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class ConnectionFactory
{
    /**
     * @throws Exception
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * @psalm-suppress MixedArgumentTypeCoercion
     */
    public function __invoke(ContainerInterface $container): Connection
    {
        $config = $container->get('config');
        assert(is_array($config));
        assert(is_array($config['databases']));
        assert(is_array($config['databases'][Connection::class]));

        return DriverManager::getConnection($config['databases'][Connection::class]);
    }
}
