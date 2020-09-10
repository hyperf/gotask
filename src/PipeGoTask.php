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

use Hyperf\GoTask\Config\DomainConfig;
use Hyperf\GoTask\IPC\PipeIPCSender;
use Hyperf\Process\ProcessCollector;
use Swoole\Coroutine\Channel;
use Swoole\Lock;
use Swoole\Process;

/**
 * Class PipeGoTask uses stdin/stdout pipes to communicate.
 * This class can be used as a singleton.
 * It is safe in multiple coroutines and multiple processes.
 */
class PipeGoTask implements GoTask
{
    /**
     * @var Lock
     */
    public $lock;

    /**
     * @var
     */
    private $taskChannel;

    /**
     * @var null|Process
     */
    private $process;

    /**
     * @var DomainConfig
     */
    private $config;

    public function __construct(DomainConfig $config, ?Process $process = null)
    {
        $this->process = $process;
        $this->config = $config;
        $this->lock = new Lock();
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
            $processName = $this->config->getProcessName();
            $this->process = ProcessCollector::get($processName)[0];
        }
        $task = make(PipeIPCSender::class, ['process' => $this->process]);
        while (true) {
            [$method, $payload, $flag, $returnChannel] = $this->taskChannel->pop();
            // check if channel is closed
            if ($method === null) {
                break;
            }
            $this->lock->lock();
            try {
                $result = $task->call($method, $payload, $flag);
                $returnChannel->push($result);
            } catch (\Throwable $e) {
                if (! ($returnChannel instanceof Channel)) {
                    throw $e;
                }
                $returnChannel->push($e);
            } finally {
                $this->lock->unlock();
            }
        }
    }
}
