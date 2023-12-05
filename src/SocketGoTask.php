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

use Hyperf\Context\Context;
use Hyperf\GoTask\Exception\InvalidGoTaskConnectionException;

use function Hyperf\Coroutine\defer;

/**
 * Class SocketGoTask uses sockets to communicate.
 * This class can be used as a singleton.
 * It is safe in multiple coroutines and multiple processes.
 */
class SocketGoTask implements GoTask
{
    public function __construct(
        private GoTaskConnectionPool $pool
    ) {
    }

    public function call(string $method, mixed $payload, int $flags = 0): mixed
    {
        $hasContextConnection = Context::has($this->getContextKey());
        $connection = $this->getConnection($hasContextConnection);
        try {
            $connection = $connection->getConnection();
            // Execute the command with the arguments.
            $result = $connection->call($method, $payload, $flags);
        } finally {
            // Release connection.
            if (! $hasContextConnection) {
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
     */
    private function getConnection(mixed $hasContextConnection): GoTaskConnection
    {
        $connection = null;
        if ($hasContextConnection) {
            $connection = Context::get($this->getContextKey());
        }
        if (! $connection instanceof GoTaskConnection) {
            $pool = $this->pool;
            $connection = $pool->get();
        }
        if (! $connection instanceof GoTaskConnection) {
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
