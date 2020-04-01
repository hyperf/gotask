<?php


namespace HyperfTest\Cases;


use Reasno\GoTask\IPC\SocketIPCReceiver;
use Reasno\GoTask\IPC\SocketIPCSender;
use Swoole\Process;

class ConfigTest extends AbstractTestCase
{

    public function setUp()
    {
        parent::setUp();
        exec('go build -o ' . __DIR__ . '/config ' . __DIR__ . '/../../example/config/config.go');
        $this->p = new Process(function (Process $process) {
            sleep(1);
            $process->exec(__DIR__ . '/config', ['-go2php_addr', self::UNIX_SOCKET]);
        });
        $this->p->start();

    }

    public function testOnCoroutine()
    {
        \Swoole\Coroutine\run(function () {
            /** @var SocketIPCReceiver $receiver */
            $receiver = null;
            go(function () use (&$receiver) {
                try {
                    $receiver = new SocketIPCReceiver(self::UNIX_SOCKET);
                    $receiver->start();
                } catch (\Throwable $e) {
                    // Not Reachable
                    $this->assertTrue(false);
                }
            });
            $sender = new SocketIPCSender(self::UNIX_SOCKET);
            $this->baseExample($sender);
            $receiver->close();
        });
    }

}
