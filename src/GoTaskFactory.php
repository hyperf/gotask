<?php


namespace Reasno\GoTask;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;
use Reasno\GoTask\Relay\CoroutineSocketRelay;

class GoTaskFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);
        $address = $config->get('gotask.socket_address', '/tmp/'.uniqid().'.sock');
        $split = explode(':', $address, 2);
        if (count($split) === 1){
            return new GoTask(
                new CoroutineSocketRelay($split[0], null, CoroutineSocketRelay::SOCK_UNIX)
            );
        }
        [$host, $port] = $split;
        return new GoTask(
            new CoroutineSocketRelay($host, (int)$port, CoroutineSocketRelay::SOCK_TCP)
        );
    }
}
