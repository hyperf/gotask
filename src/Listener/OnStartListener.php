<?php


namespace Reasno\GoTask\Listener;


use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\Framework\Event\OnStart;
use Hyperf\Process\Exception\SocketAcceptException;
use Hyperf\Process\ProcessCollector;
use Psr\Container\ContainerInterface;
use Swoole\Coroutine;

class OnStartListener implements ListenerInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    /**
     * @inheritDoc
     */
    public function listen(): array
    {
        return [OnStart::class];
    }

    /**
     * @inheritDoc
     */
    public function process(object $event)
    {
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
