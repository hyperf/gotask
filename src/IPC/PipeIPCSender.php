<?php

declare(strict_types=1);
/**
 * This file is part of Reasno/GoTask.
 *
 * @link     https://www.github.com/reasno/gotask
 * @document  https://www.github.com/reasno/gotask
 * @contact  guxi99@gmail.com
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace Reasno\GoTask\IPC;

use Reasno\GoTask\GoTask;
use Reasno\GoTask\Relay\ProcessPipeRelay;
use Spiral\Goridge\RPC;

/**
 * Class PipeIPC uses pipes to communicate.
 * It can only be used in one coroutine.
 */
class PipeIPCSender implements IPCSenderInterface, GoTask
{
    /**
     * @var RPC
     */
    private $handler;

    /**
     * PipeIPC constructor.
     * @param $process
     * @mixin RPC
     */
    public function __construct($process)
    {
        $this->handler = new RPC(
            new ProcessPipeRelay($process)
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
