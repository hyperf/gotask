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

use Hyperf\Contract\ConnectionInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\GoTask\IPC\SocketIPCSender;
use Hyperf\Pool\Connection;
use Hyperf\Pool\Exception\ConnectionException;
use Hyperf\Pool\Pool;
use Psr\Container\ContainerInterface;
use Spiral\Goridge\RPC;
use Throwable;

/**
 * Class GoTaskConnection.
 * @mixin RPC
 */
class GoTaskConnection extends Connection implements ConnectionInterface
{
    private SocketIPCSender $connection;

    public function __construct(ContainerInterface $container, Pool $pool, private SocketIPCFactory $factory)
    {
        parent::__construct($container, $pool);
        $this->reconnect();
    }

    public function __call(string $name, array $arguments): mixed
    {
        try {
            $result = $this->connection->{$name}(...$arguments);
        } catch (Throwable $exception) {
            $result = $this->retry($name, $arguments, $exception);
        }

        return $result;
    }

    public function close(): bool
    {
        unset($this->connection);
        return true;
    }

    public function reconnect(): bool
    {
        $this->connection = $this->factory->make();
        $this->lastUseTime = microtime(true);
        return true;
    }

    public function getActiveConnection(): self
    {
        if ($this->check()) {
            return $this;
        }

        if (! $this->reconnect()) {
            throw new ConnectionException('Connection reconnect failed.');
        }

        return $this;
    }

    protected function retry($name, $arguments, Throwable $exception): mixed
    {
        $logger = $this->container->get(StdoutLoggerInterface::class);
        $logger->warning(sprintf('RemoteGoTask::__call failed, because ' . $exception->getMessage()));

        try {
            $this->reconnect();
            $result = $this->connection->{$name}(...$arguments);
        } catch (Throwable $exception) {
            $this->lastUseTime = 0.0;
            throw $exception;
        }

        return $result;
    }
}
