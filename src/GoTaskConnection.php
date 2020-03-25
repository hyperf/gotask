<?php


namespace Reasno\GoTask;

use Hyperf\Contract\ConnectionInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Pool\Connection;
use Hyperf\Pool\Exception\ConnectionException;
use Hyperf\Pool\Pool;
use Psr\Container\ContainerInterface;
use Spiral\Goridge\RPC;

/**
 * Class GoTaskConnection
 * @package Reasno\Gotask
 * @mixin RPC
 */
class GoTaskConnection extends Connection implements ConnectionInterface
{
    /**
     * @var RPC
     */
    private $connection;

    public function __construct(ContainerInterface $container, Pool $pool)
    {
        parent::__construct($container, $pool);
        $this->reconnect();
    }

    public function close(): bool
    {
        unset($this->connection);
        return true;
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

    /**
     * @return bool
     */
    public function reconnect(): bool
    {
        $this->connection = make(RPC::class);
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
        $logger->warning(sprintf('GoTask::__call failed, because ' . $exception->getMessage()));

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
