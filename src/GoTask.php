<?php


namespace Reasno\GoTask;

use Hyperf\Utils\Context;
use Reasno\Gotask\Exception\InvalidGoTaskConnectionException;

class GoTask
{
    /**
     * @var GoTaskConnectionPool
     */
    private $pool;

    public function __construct(GoTaskConnectionPool $pool)
    {
        $this->pool = $pool;
    }

    public function call(string $method, $payload, int $flags = 0)
    {
        $hasContextConnection = Context::has($this->getContextKey());
        $connection = $this->getConnection($hasContextConnection);
        try {
            $connection = $connection->getConnection();
            // Execute the command with the arguments.
            $result = $connection->call($method, $payload, $flags);
        } finally {
            // Release connection.
            if (!$hasContextConnection) {
                Context::set($this->getContextKey(), $connection);
                defer(function () use ($connection) {
                    $connection->release();
                });
            }
        }
        return $result;
    }

    /**
     * Get a connection from coroutine context, or from redis connectio pool.
     * @param mixed $hasContextConnection
     */
    private function getConnection($hasContextConnection): GoTaskConnection
    {
        $connection = null;
        if ($hasContextConnection) {
            $connection = Context::get($this->getContextKey());
        }
        if (!$connection instanceof GoTaskConnection) {
            $pool = $this->pool;
            $connection = $pool->get();
        }
        if (!$connection instanceof GoTaskConnection) {
            throw new InvalidGoTaskConnectionException('The connection is not a valid RedisConnection.');
        }
        return $connection;
    }

    /**
     * The key to identify the connection object in coroutine context.
     */
    private function getContextKey(): string
    {
        return 'gotask.connection';
    }

}
