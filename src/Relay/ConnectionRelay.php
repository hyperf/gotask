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
namespace Hyperf\GoTask\Relay;

use Spiral\Goridge\Exceptions\RelayException;
use Swoole\Coroutine\Server\Connection;
use Swoole\Coroutine\Socket;

class ConnectionRelay implements RelayInterface
{
    use SocketTransporter;

    private $conn;

    /**
     * @var Socket
     */
    private $socket;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Ensure socket connection. Returns true if socket successfully connected
     * or have already been connected.
     *
     * @throws RelayException
     * @throws \Error when sockets are used in unsupported environment
     */
    public function connect(): bool
    {
        if ($this->isConnected()) {
            return true;
        }

        $this->socket = $this->conn->exportSocket();
        return true;
    }
}
