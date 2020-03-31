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

namespace Reasno\GoTask\Listener;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\Framework\Event\AfterWorkerStart;
use Hyperf\Framework\Event\MainWorkerStart;
use Hyperf\Process\Exception\SocketAcceptException;
use Hyperf\Process\ProcessCollector;
use Psr\Container\ContainerInterface;
use Reasno\GoTask\IPC\SocketIPCReceiver;
use Swoole\Coroutine;

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
        if ($this->config->get('gotask.go2php.enable', false)) {
            $addr = $this->config->get('gotask.go2php.address', '/tmp/gotask_go2php.sock');
            $server = make(SocketIPCReceiver::class, $addr);
            $server->start();
        }
    }
}
