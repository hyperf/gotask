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

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

class GoTaskFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);
        if ($config->get('gotask.socket_address', false)) {
            return $container->get(SocketGoTask::class);
        }
        return $container->get(PipeGoTask::class);
    }
}
