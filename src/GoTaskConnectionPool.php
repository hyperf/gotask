<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf/GoTask.
 *
 * @link     https://www.github.com/hyperf/gotask
 * @document  https://www.github.com/hyperf/gotask
 * @contact  guxi99@gmail.com
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Hyperf\GoTask;

use Hyperf\Contract\ConnectionInterface;
use Hyperf\GoTask\Config\DomainConfig;
use Hyperf\Pool\Frequency;
use Hyperf\Pool\Pool;
use Psr\Container\ContainerInterface;

use function Hyperf\Support\make;

class GoTaskConnectionPool extends Pool
{
    public function __construct(ContainerInterface $container, DomainConfig $config)
    {
        $options = $config->getPoolOptions();
        $this->frequency = make(Frequency::class);
        parent::__construct($container, $options);
    }

    public function createConnection(): ConnectionInterface
    {
        return make(GoTaskConnection::class, ['pool' => $this]);
    }
}
