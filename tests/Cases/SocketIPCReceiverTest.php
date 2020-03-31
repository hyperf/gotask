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

namespace HyperfTest\Cases;

use Hyperf\Utils\WaitGroup;
use Reasno\GoTask\IPC\SocketIPCReceiver;
use Reasno\GoTask\IPC\SocketIPCSender;
use Reasno\GoTask\Relay\RelayInterface;
use Spiral\Goridge\Exceptions\ServiceException;

/**
 * @internal
 * @coversNothing
 */
class SocketIPCReceiverTest extends AbstractTestCase
{
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

    public function testConcurrently()
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
                    var_dump($e);
                    $this->assertTrue(false);
                }
            });
            $wg = new WaitGroup();
            $wg->add();
            go(function () use ($wg) {
                $sender = new SocketIPCSender(self::UNIX_SOCKET);
                $this->baseExample($sender);
                $wg->done();
            });
            $wg->add();
            go(function () use ($wg) {
                $sender = new SocketIPCSender(self::UNIX_SOCKET);
                $this->baseExample($sender);
                $wg->done();
            });
            $wg->add();
            go(function () use ($wg) {
                $sender = new SocketIPCSender(self::UNIX_SOCKET);
                $this->baseExample($sender);
                $wg->done();
            });
            $wg->wait();
            $receiver->close();
        });
    }

    public function baseExample($task)
    {
        $this->assertEquals(
            'Hello, Reasno!',
            $task->call('HyperfTest\\Stub::HelloString', 'Reasno')
        );
        $this->assertEquals(
            ['hello' => ['jack', 'jill']],
            $task->call('HyperfTest\\Stub::HelloInterface', ['jack', 'jill'])
        );
        $this->assertEquals(
            ['hello' => [
                'firstName' => 'LeBron',
                'lastName' => 'James',
                'id' => 23,
            ]],
            $task->call('HyperfTest\\Stub::HelloStruct', [
                'firstName' => 'LeBron',
                'lastName' => 'James',
                'id' => 23,
            ])
        );

        $this->assertEquals(
            'My Bytes',
            $task->call('HyperfTest\\Stub::HelloBytes', base64_encode('My Bytes'), RelayInterface::PAYLOAD_RAW)
        );
        try {
            $task->call('HyperfTest\\Stub::HelloError', 'Reasno');
        } catch (\Throwable $e) {
            $this->assertInstanceOf(ServiceException::class, $e);
        }
    }
}
