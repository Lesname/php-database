<?php

declare(strict_types=1);

namespace LesDatabase\Config;

use Doctrine\DBAL\Connection;
use LesDatabase\Factory\ConnectionFactory;

/**
 * @psalm-immutable
 */
final class ConfigProvider
{
    /**
     * @return array<string, mixed>
     *
     * @psalm-pure
     */
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                'factories' => [
                    Connection::class => ConnectionFactory::class,
                ],
            ],
        ];
    }
}
