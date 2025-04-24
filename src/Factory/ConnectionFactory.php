<?php
declare(strict_types=1);

namespace LesDatabase\Factory;

use Psr\Log\LoggerInterface;
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
    public function __invoke(ContainerInterface $container, string $name): Connection
    {
        $config = $container->get('config');

        assert(is_array($config));
        assert(is_array($config['databases']));
        assert(is_array($config['databases'][$name]));

        $params = $config['databases'][$name];

        if (isset($params['url'])) {
            $this->log(
                $container,
                sprintf(
                    "Update database connection setup for %s",
                    $name,
                ),
            );
        } elseif (!isset($params['charset'])) {
            $this->log(
                $container,
                sprintf(
                    "No charset for %s",
                    $name,
                ),
            );
        } elseif ($params['charset'] !== 'UTF8MB4') {
            $this->log(
                $container,
                sprintf(
                    "Charset UTF8MB4 recommended for %s",
                    $name,
                ),
            );
        }

        // @phpstan-ignore argument.type
        return DriverManager::getConnection($config['databases'][Connection::class]);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function log(ContainerInterface $container, string $message): void
    {
        /** @var LoggerInterface $logger */
        $logger = $container->get(LoggerInterface::class);

        $logger->warning($message);
    }
}
