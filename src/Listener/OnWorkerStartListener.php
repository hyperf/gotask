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

namespace Hyperf\GoTask\Listener;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\AfterWorkerStart;
use Hyperf\GoTask\IPC\SocketIPCReceiver;
use Psr\Container\ContainerInterface;

class OnWorkerStartListener implements ListenerInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->config = $container->get(ConfigInterface::class);
    }

    /**
     * {@inheritdoc}
     */
    public function listen(): array
    {
        return [AfterWorkerStart::class];
    }

    /**
     * {@inheritdoc}
     */
    public function process(object $event)
    {
        if (! $this->config->get('gotask.go2php.enable', false)) {
            return;
        }
        $addr = $this->config->get('gotask.go2php.address', '/tmp/gotask_go2php.sock');
        if ($this->isUnix($addr)) {
            $addrArr = explode(',', $addr);
            if (count($addrArr) <= $event->workerId) {
                return;
            }
            $addr = $addrArr[$event->workerId];
        }
        $server = make(SocketIPCReceiver::class, [$addr]);
        $server->start();
    }

    private function isUnix(string $addr): bool
    {
        return strpos($addr, ':') === false;
    }
}
