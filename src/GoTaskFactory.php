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
