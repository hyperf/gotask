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
namespace Hyperf\GoTask\IPC;

use Hyperf\GoTask\GoTask;
use Hyperf\GoTask\Relay\CoroutineSocketRelay;
use Spiral\Goridge\RPC;

/**
 * Class SocketIPC uses sockets to communicate.
 * It can only be used in one coroutine.
 */
class SocketIPCSender implements IPCSenderInterface, GoTask
{
    /**
     * @var RPC
     */
    private $handler;

    /**
     * PipeIPC constructor.
     * @mixin RPC
     */
    public function __construct(string $address = '127.0.0.1:6001')
    {
        $split = explode(':', $address, 2);
        if (count($split) === 1) {
            $this->handler = new RPC(
                new CoroutineSocketRelay($split[0], 0, CoroutineSocketRelay::SOCK_UNIX)
            );
            return;
        }
        [$host, $port] = $split;
        $this->handler = new RPC(
            new CoroutineSocketRelay($host, (int) $port, CoroutineSocketRelay::SOCK_TCP)
        );
    }

    public function __call($name, $arguments)
    {
        $this->handler->{$name}(...$arguments);
    }

    public function call(string $method, $payload, int $flags = 0)
    {
        return $this->handler->call($method, $payload, $flags);
    }
}
