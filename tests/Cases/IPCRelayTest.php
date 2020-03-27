<?php


namespace HyperfTest\Cases;


use Hyperf\Utils\WaitGroup;
use Reasno\GoTask\LocalGoTask;
use Reasno\GoTask\Relay\CoroutineSocketRelay;
use Reasno\GoTask\Relay\IPCRelay;
use Reasno\GoTask\Relay\RelayInterface;
use Spiral\Goridge\Exceptions\ServiceException;
use Spiral\Goridge\RPC;
use Swoole\Process;

class IPCRelayTest extends AbstractTestCase
{
    /**
     * @var Process
     */
    private $p;

    public function setUp()
    {
        $this->p = new Process(function (Process $process) {
            $process->exec(__DIR__ . '/../../app', []);
        }, true);
        $this->p->start();
        sleep(1);
    }

    public function testOnCoroutine()
    {
        \Swoole\Coroutine\run(function () {
            $task = new RPC(
                new IPCRelay($this->p)
            );
            $this->baseExample($task);
        });
    }

    public function testConcurrently()
    {
        \Swoole\Coroutine\run(function () {
            sleep(1);
            $task = make(LocalGoTask::class, [
                'process' => $this->p,
            ]);
            $wg = new WaitGroup();
            $wg->add();
            go(function() use ($wg, $task) {
                $this->baseExample($task);
                $wg->done();
            });
            $wg->add();
            go(function() use ($wg, $task) {
                $this->baseExample($task);
                $wg->done();
            });
            $wg->wait();
        });
    }

    public function baseExample($task){
        $this->assertEquals(
            'Hello, Reasno!',
            $task->call('App.HelloString', 'Reasno')
        );
        $this->assertEquals(
            ['hello' => ['jack', 'jill']],
            $task->call('App.HelloInterface', ['jack', 'jill'])
        );
        $this->assertEquals(
            ['hello' => [
                'firstName' => 'LeBron',
                'lastName' => 'James',
                'id' => 23,
            ]],
            $task->call('App.HelloStruct', [
                'firstName' => 'LeBron',
                'lastName' => 'James',
                'id' => 23,
            ])
        );

        $this->assertEquals(
            'My Bytes',
            $task->call('App.HelloBytes', base64_encode('My Bytes'), RelayInterface::PAYLOAD_RAW)
        );
        try {
            $task->call('App.HelloError', 'Reasno');
        } catch (\Throwable $e) {
            $this->assertInstanceOf(ServiceException::class, $e);
        }
    }
}
