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

use Hyperf\GoTask\Config\DomainConfig;
use Psr\Container\ContainerInterface;

class GoTaskFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(DomainConfig::class);
        if ($config->getAddress()) {
            return $container->get(SocketGoTask::class);
        }
        return $container->get(PipeGoTask::class);
    }
}
