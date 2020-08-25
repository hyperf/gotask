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

use Spiral\Goridge\Exceptions\GoridgeException;
use Spiral\Goridge\Exceptions\InvalidArgumentException;
use Spiral\Goridge\Exceptions\RelayException;
use Swoole\Coroutine\Socket;

/**
 * Communicates with remote server/client over be-directional socket using byte payload:.
 *
 * [ prefix       ][ payload                               ]
 * [ 1+8+8 bytes  ][ message length|LE ][message length|BE ]
 *
 * prefix:
 * [ flag       ][ message length, unsigned int 64bits, LittleEndian ]
 */
class CoroutineSocketRelay implements RelayInterface
{
    use SocketTransporter;

    /** Supported socket types. */
    const SOCK_TCP = 0;

    const SOCK_UNIX = 1;

    // @deprecated
    const SOCK_TPC = self::SOCK_TCP;

    /** @var string */
    private $address;

    /** @var null|int */
    private $port;

    /** @var int */
    private $type;

    /** @var Socket */
    private $socket;

    /**
     * Example:
     * $relay = new SocketRelay("localhost", 7000);
     * $relay = new SocketRelay("/tmp/rpc.sock", null, Socket::UNIX_SOCKET);.
     *
     * @param string $address localhost, ip address or hostname
     * @param null|int $port ignored for UNIX sockets
     * @param int $type Default: TCP_SOCKET
     *
     * @throws InvalidArgumentException
     */
    public function __construct(string $address, int $port = null, int $type = self::SOCK_TCP)
    {
        switch ($type) {
            case self::SOCK_TCP:
                if ($port === null) {
                    throw new InvalidArgumentException(sprintf(
                        "no port given for TPC socket on '%s'",
                        $address
                    ));
                }
                break;
            case self::SOCK_UNIX:
                $port = null;
                break;
            default:
                throw new InvalidArgumentException(sprintf(
                    "undefined connection type %s on '%s'",
                    $type,
                    $address
                ));
        }

        $this->address = $address;
        $this->port = $port;
        $this->type = $type;
    }

    public function __toString(): string
    {
        if ($this->type == self::SOCK_TCP) {
            return "tcp://{$this->address}:{$this->port}";
        }

        return "unix://{$this->address}";
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @return null|int
     */
    public function getPort()
    {
        return $this->port;
    }

    public function getType(): int
    {
        return $this->type;
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
        try {
            // Port type needs to be int, so we convert null to 0
            if ($this->socket->connect($this->address, $this->port ?? 0) === false) {
                throw new RelayException(sprintf('%s (%s)', $this->socket->errMsg, $this->socket->errCode));
            }
        } catch (\Exception $e) {
            throw new RelayException("unable to establish connection {$this}: {$e->getMessage()}", 0, $e);
        }

        return true;
    }

    /**
     * @throws GoridgeException
     * @return Socket
     */
    private function createSocket()
    {
        if ($this->type === self::SOCK_UNIX) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                throw new GoridgeException("socket {$this} unavailable on Windows");
            }
            return new Socket(AF_UNIX, SOCK_STREAM, 0);
        }

        return new Socket(AF_INET, SOCK_STREAM, IPPROTO_IP);
    }
}
