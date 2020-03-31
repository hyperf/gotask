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
use Reasno\GoTask\IPC\SocketIPCSender;

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
