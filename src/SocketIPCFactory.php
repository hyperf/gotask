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
use Hyperf\GoTask\IPC\SocketIPCSender;
use Psr\Container\ContainerInterface;

class SocketIPCFactory
{
    const DEFAULT_ADDR = '/tmp/gotask.sock';

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function make()
    {
        if (! $this->container->has(ConfigInterface::class)) {
            return make(SocketIPCSender::class, ['address' => self::DEFAULT_ADDR]);
        }
        $config = $this->container->get(ConfigInterface::class);
        $address = $config->get('gotask.socket_address', '/tmp/gotask.sock');
        return make(SocketIPCSender::class, ['address' => $address]);
    }
}
