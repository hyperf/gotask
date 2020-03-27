<?php

declare(strict_types=1);
/**
 * This file is part of Reasno/RemoteGoTask.
 *
 * @link     https://www.github.com/reasno/gotask
 * @document  https://www.github.com/reasno/gotask
 * @contact  guxi99@gmail.com
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Reasno\GoTask;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use Reasno\GoTask\Relay\CoroutineSocketRelay;
use Spiral\Goridge\RPC;

class RPCFactory
{
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
        $config = $this->container->get(ConfigInterface::class);
        $address = $config->get('gotask.socket_address', '/tmp/gotask.sock');
        $split = explode(':', $address, 2);
        if (count($split) === 1) {
            return new RPC(
                new CoroutineSocketRelay($split[0], 0, CoroutineSocketRelay::SOCK_UNIX)
            );
        }
        [$host, $port] = $split;
        return new RPC(
            new CoroutineSocketRelay($host, (int) $port, CoroutineSocketRelay::SOCK_TCP)
        );
    }
}
