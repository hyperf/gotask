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

namespace Reasno\GoTask\Process;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Exception\SocketAcceptException;
use Hyperf\Process\ProcessCollector;
use Psr\Container\ContainerInterface;
use Reasno\GoTask\Exception\GoBuildException;
use Swoole\Coroutine;
use Swoole\Process;
use Swoole\Server;

class GoTaskProcess extends AbstractProcess
{
    /**
     * @var string
     */
    public $name = 'gotask';

    public $redirectStdinStdout = true;

    public $enableCoroutine = true;

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->config = $container->get(ConfigInterface::class);
        $this->redirectStdinStdout = empty($this->config->get('gotask.socket_address')) ?? false;
    }

    public function isEnable(): bool
    {
        return $this->config->get('gotask.enable', false);
    }

    public function bind(Server $server): void
    {
        if ($this->config->get('gotask.go_build.enable', false)) {
            $result = exec($this->config->get('gotask.go_build.command'));
            if ($result->code !== 0) {
                throw new GoBuildException(sprintf(
                    'Cannot build go files with command %s: %s',
                    $this->config->get('gotask.go_build.command'),
                    $result->output
                ));
            }
        }
        parent::bind($server);
        /** @var Process $process */
        $process = ProcessCollector::get($this->name)[0];
        Coroutine::create(function () {
            $sock = $this->process->exportSocket();
            while (true) {
                try {
                    /** @var \Swoole\Coroutine\Socket $sock */
                    $recv = $sock->recv($this->recvLength, $this->recvTimeout);
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

    /**
     * {@inheritdoc}
     */
    public function handle(): void
    {
        $executable = $this->config->get('gotask.executable', BASE_PATH . '/app');
        $address = $this->config->get('gotask.socket_address', '/tmp/gotask.sock');
        $args = $this->config->get('gotask.args', []);
        $argArr = ['-address', $address];
        array_push($argArr, ...$args);
        $this->process->exec($executable, $argArr);
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
