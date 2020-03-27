<?php


namespace Reasno\GoTask\Relay;

use Spiral\Goridge\Exceptions\GoridgeException;
use Spiral\Goridge\Exceptions\RelayException;
use Swoole\Coroutine\Socket;
use Swoole\Process;

/**
 * Communicates with remote server/client over be-directional socket using byte payload:.
 *
 * [ prefix       ][ payload                               ]
 * [ 1+8+8 bytes  ][ message length|LE ][message length|BE ]
 *
 * prefix:
 * [ flag       ][ message length, unsigned int 64bits, LittleEndian ]
 */
class IPCRelay implements RelayInterface
{
    use SocketTransporter;

    /** @var Socket */
    private $socket;
    /**
     * @var Process
     */
    private $process;

    /**
     * export a socket from swoole process
     *
     * @param Process $process
     */
    public function __construct(Process $process)
    {
        $this->process = $process;
        $this->socket = null;
    }

    public function __toString(): string
    {
        return 'socket_fd:' . $this->socket->fd;
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

        $this->socket = $this->createSocket();
        return true;
    }

    /**
     * @throws GoridgeException
     * @return Socket
     */
    private function createSocket()
    {
        return $this->process->exportSocket();
    }
}

