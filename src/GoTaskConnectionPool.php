<?php

declare(strict_types=1);
/**
 * This file is part of Reasno/GoTask.
 *
 * @link     https://www.github.com/reasno/gotask
 * @document  https://www.github.com/reasno/gotask
 * @contact  guxi99@gmail.com
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Reasno\GoTask;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ConnectionInterface;
use Hyperf\Pool\Frequency;
use Hyperf\Pool\Pool;
use Psr\Container\ContainerInterface;

class GoTaskConnectionPool extends Pool
{
    public function __construct(ContainerInterface $container)
    {
        $options = $this->getConfig($container);
        $this->frequency = make(Frequency::class);
        parent::__construct($container, $options);
    }

    public function createConnection(): ConnectionInterface
    {
        return make(GoTaskConnection::class, ['pool' => $this]);
    }

    protected function getConfig(ContainerInterface $container)
    {
        if (! $container->has(ConfigInterface::class)) {
            return [];
        }
        $config = $container->get(ConfigInterface::class);
        return $config->get('gotask.pool', []);
    }
}
