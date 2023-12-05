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
use Hyperf\GoTask\Relay\ProcessPipeRelay;
use Spiral\Goridge\RPC;
use Swoole\Process;

/**
 * Class PipeIPC uses pipes to communicate.
 * It can only be used in one coroutine.
 */
class PipeIPCSender implements IPCSenderInterface, GoTask
{
    private RPC $handler;

    /**
     * PipeIPC constructor.
     * @mixin RPC
     */
    public function __construct(Process $process)
    {
        $this->handler = new RPC(
            new ProcessPipeRelay($process)
        );
    }

    public function __call(string $name, array $arguments): void
    {
        $this->handler->{$name}(...$arguments);
    }

    public function call(string $method, $payload, int $flags = 0): mixed
    {
        return $this->handler->call($method, $payload, $flags);
    }
}
