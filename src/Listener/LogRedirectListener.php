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

use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\Framework\Event\MainWorkerStart;
use Hyperf\GoTask\Config\DomainConfig;
use Hyperf\Process\Exception\SocketAcceptException;
use Hyperf\Process\ProcessCollector;
use Psr\Container\ContainerInterface;
use Swoole\Coroutine;

class LogRedirectListener implements ListenerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var DomainConfig
     */
    private $config;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->config = $container->get(DomainConfig::class);
    }

    /**
     * {@inheritdoc}
     */
    public function listen(): array
    {
        return [MainWorkerStart::class];
    }

    /**
     * {@inheritdoc}
     */
    public function process(object $event)
    {
        if (! $this->config->shouldLogRedirect()) {
            return;
        }
        Coroutine::create(function () {
            $processes = ProcessCollector::get('gotask');
            if (empty($processes)) {
                return;
            }
            $sock = $processes[0]->exportSocket();
            while (true) {
                try {
                    /* @var \Swoole\Coroutine\Socket $sock */
                    $recv = $sock->recv();
                    if ($recv === '') {
                        throw new SocketAcceptException('Socket is closed', $sock->errCode);
                    }
                    if ($recv === false && $sock->errCode !== SOCKET_ETIMEDOUT) {
                        throw new SocketAcceptException('Socket is closed', $sock->errCode);
                    }
                    if ($recv !== false) {
                        $this->logOutput((string) $recv);
                    }
                } catch (\Throwable $exception) {
                    $this->logThrowable($exception);
                    if ($exception instanceof SocketAcceptException) {
                        break;
                    }
                }
            }
        });
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
            $level = $this->config->getLogLevel();
            $logger->{$level}($output);
        }
    }
}
