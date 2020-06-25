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

use Spiral\Goridge\Exceptions\PrefixException;
use Spiral\Goridge\Exceptions\RelayException;
use Spiral\Goridge\Exceptions\TransportException;

trait SocketTransporter
{
    /**
     * Destruct connection and disconnect.
     */
    public function __destruct()
    {
        if ($this->isConnected()) {
            $this->close();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function send($payload, int $flags = null)
    {
        $this->connect();

        $size = $payload === null ? 0 : strlen($payload);
        if ($flags & self::PAYLOAD_NONE && $size != 0) {
            throw new TransportException('unable to send payload with PAYLOAD_NONE flag');
        }

        $body = pack('CPJ', $flags, $size, $size);

        if (! ($flags & self::PAYLOAD_NONE)) {
            $body .= $payload;
        }

        $this->socket->send($body);
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function receiveSync(int &$flags = null)
    {
        $this->connect();

        $prefix = $this->fetchPrefix();
        $flags = $prefix['flags'];
        $result = null;

        if ($prefix['size'] !== 0) {
            $readBytes = $prefix['size'];
            $buffer = null;

            //Add ability to write to stream in a future
            while ($readBytes > 0) {
                $buffer = $this->socket->recv(min(self::BUFFER_SIZE, $readBytes));
                $result .= $buffer;
                $readBytes -= strlen($buffer);
            }
        }
        return $result;
    }

    public function isConnected(): bool
    {
        return $this->socket != null;
    }

    /**
     * Close connection.
     *
     * @throws RelayException
     */
    public function close()
    {
        if (! $this->isConnected()) {
            throw new RelayException("unable to close socket '{$this}', socket already closed");
        }

        $this->socket->close();
        $this->socket = null;
    }

    /**
     * @throws PrefixException
     * @return array Prefix [flag, length]
     */
    private function fetchPrefix(): array
    {
        $prefixBody = $this->socket->recv(17);
        if ($prefixBody === false || strlen($prefixBody) !== 17) {
            throw new PrefixException(sprintf(
                'unable to read prefix from socket: %s (%s)',
                $this->socket->errMsg,
                $this->socket->errCode
            ));
        }

        $result = unpack('Cflags/Psize/Jrevs', $prefixBody);
        if (! is_array($result)) {
            throw new PrefixException('invalid prefix');
        }

        if ($result['size'] != $result['revs']) {
            throw new PrefixException('invalid prefix (checksum)');
        }

        return $result;
    }
}
