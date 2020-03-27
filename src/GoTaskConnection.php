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

use Hyperf\Contract\ConnectionInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Pool\Connection;
use Hyperf\Pool\Exception\ConnectionException;
use Hyperf\Pool\Pool;
use Psr\Container\ContainerInterface;
use Spiral\Goridge\RPC;

/**
 * Class GoTaskConnection.
 * @mixin RPC
 */
class GoTaskConnection extends Connection implements ConnectionInterface
{
    /**
     * @var RPC
     */
    private $connection;
    /**
     * @var RPCFactory
     */
    private $factory;

    public function __construct(ContainerInterface $container, Pool $pool, RPCFactory $factory)
    {
        parent::__construct($container, $pool);
        $this->factory = $factory;
        $this->reconnect();
    }

    public function __call($name, $arguments)
    {
        try {
            $result = $this->connection->{$name}(...$arguments);
        } catch (\Throwable $exception) {
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

    public function getActiveConnection()
    {
        if ($this->check()) {
            return $this;
        }

        if (! $this->reconnect()) {
            throw new ConnectionException('Connection reconnect failed.');
        }

        return $this;
    }

    protected function retry($name, $arguments, \Throwable $exception)
    {
        $logger = $this->container->get(StdoutLoggerInterface::class);
        $logger->warning(sprintf('RemoteGoTask::__call failed, because ' . $exception->getMessage()));

        try {
            $this->reconnect();
            $result = $this->connection->{$name}(...$arguments);
        } catch (\Throwable $exception) {
            $this->lastUseTime = 0.0;
            throw $exception;
        }

        return $result;
    }
}
