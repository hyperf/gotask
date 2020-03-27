<?php


namespace Reasno\GoTask;

use Hyperf\Process\ProcessCollector;
use Reasno\GoTask\Relay\IPCRelay;
use Spiral\Goridge\RPC;
use Swoole\Coroutine\Channel;
use Swoole\Process;

class LocalGoTask implements GoTask
{
    /**
     * @var
     */
    private $taskChannel;
    /**
     * @var Process|null
     */
    private $process;

    public function __construct(?Process $process)
    {
        $this->process= $process;
    }

    public function call(string $method, $payload, int $flags = 0){
        if ($this->taskChannel == null){
            $this->taskChannel = new Channel(100);
            go(function(){
                $this->start();
            });
        }
        $returnChannel = new Channel(1);
        $this->taskChannel->push([$method, $payload, $flags, $returnChannel]);
        $result = $returnChannel->pop();
        if ($result instanceof \Throwable){
            throw $result;
        }
        return $result;
    }

    private function start()
    {
        if ($this->process == null){
            $this->process = ProcessCollector::get('gotask')[0];
        }
        $task = new RPC(
            new IPCRelay($this->process)
        );
        while (true) {
            [$method, $payload, $flag, $returnChannel] = $this->taskChannel->pop();
            try{
                $result = $task->call($method, $payload, $flag);
                $returnChannel->push($result);
            } catch (\Throwable $e){
                $returnChannel->push($e);
            }


        }
    }
}
