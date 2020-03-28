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

namespace Reasno\GoTask;

use Hyperf\Process\ProcessCollector;
use Reasno\GoTask\IPC\PipeIPC;
use Swoole\Coroutine\Channel;
use Swoole\Lock;
use Swoole\Process;

/**
 * Class PipeGoTask uses stdin/stdout pipes to communicate.
 * This class can be used as a singleton.
 * It is safe in multiple coroutines and multiple processes.
 * @package Reasno\GoTask
 */
class PipeGoTask implements GoTask
{
    /**
     * @var Lock
     */
    public static $lock;

    /**
     * @var
     */
    private $taskChannel;

    /**
     * @var null|Process
     */
    private $process;

    public function __construct(?Process $process)
    {
        $this->process = $process;
    }

    public function call(string $method, $payload, int $flags = 0)
    {
        if ($this->taskChannel == null) {
            $this->taskChannel = new Channel(100);
            go(function () {
                $this->start();
            });
        }
        $returnChannel = new Channel(1);
        $this->taskChannel->push([$method, $payload, $flags, $returnChannel]);
        $result = $returnChannel->pop();
        if ($result instanceof \Throwable) {
            throw $result;
        }
        return $result;
    }

    private function start()
    {
        if ($this->process == null) {
            $this->process = ProcessCollector::get('gotask')[0];
        }
        $task = make(PipeIPC::class, ['process' => $this->process]);
        while (true) {
            [$method, $payload, $flag, $returnChannel] = $this->taskChannel->pop();
            self::$lock->lock();
            try {
                $result = $task->call($method, $payload, $flag);
                $returnChannel->push($result);
            } catch (\Throwable $e) {
                $returnChannel->push($e);
            } finally {
                self::$lock->unlock();
            }
        }
    }
}
