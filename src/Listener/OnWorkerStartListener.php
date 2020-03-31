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
        if ($event->workerId === 0){
            Coroutine::create(function () {
                $process = ProcessCollector::get('gotask')[0];
                $sock = $process->exportSocket();
                while (true) {
                    try {
                        /** @var \Swoole\Coroutine\Socket $sock */
                        $recv = $sock->recv();
                        if ($recv === '') {
                            throw new SocketAcceptException('Socket is closed', $sock->errCode);
                        }
                        if ($recv === false && $sock->errCode !== SOCKET_ETIMEDOUT) {
                            throw new SocketAcceptException('Socket is closed', $sock->errCode);
                        }
                        $this->logOutput((string) $recv);
                    } catch (\Throwable $exception) {
                        $this->logThrowable($exception);
                        if ($exception instanceof SocketAcceptException) {
                            break;
                        }
                    }
                }
            });
        }
    }

    protected function logThrowable(\Throwable $throwable): void
    {
        if ($this->container->has(StdoutLoggerInterface::class) && $this->container->has(FormatterInterface::class)) {
            $logger = $this->container->get(StdoutLoggerInterface::class);
            $formatter = $this->container->get(FormatterInterface::class);
            $logger->error($formatter->format($throwable));

            if ($throwable instanceof SocketAcceptException) {
                $logger->critical('Socket of process is unavailable, please restart the server');
            }
        }
    }

    protected function logOutput(string $output): void
    {
        if ($this->container->has(StdoutLoggerInterface::class)) {
            $logger = $this->container->get(StdoutLoggerInterface::class);
            $logger->info($output);
        }
    }
}
