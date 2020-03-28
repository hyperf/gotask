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

use Hyperf\Config\Config;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Container;
use Hyperf\Di\Definition\DefinitionSource;
use Hyperf\Di\Definition\ScanConfig;
use Hyperf\Framework\Logger\StdoutLogger;
use Hyperf\Utils\ApplicationContext;
use Hyperf\Utils\WaitGroup;
use Reasno\GoTask\Relay\CoroutineSocketRelay;
use Reasno\GoTask\Relay\RelayInterface;
use Reasno\GoTask\SocketGoTask;
use Reasno\GoTask\SocketIPCFactory;
use Spiral\Goridge\Exceptions\ServiceException;
use Spiral\Goridge\RPC;
use Swoole\Process;

/**
 * @internal
 * @coversNothing
 */
class CoroutineSocketTest extends AbstractTestCase
{
    const UNIX_SOCKET = '/tmp/test.sock';

    /**
     * @var Process
     */
    private $p;

    public function setUp()
    {
        ! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));
        @unlink(self::UNIX_SOCKET);
        $this->p = new Process(function (Process $process) {
            $process->exec(__DIR__ . '/../../app', ['-address', self::UNIX_SOCKET]);
        });
        $this->p->start();
        sleep(1);
    }

    public function tearDown()
    {
        Process::kill($this->p->pid);
    }

    public function testOnCoroutine()
    {
        \Swoole\Coroutine\run(function () {
            $task = new RPC(
                new CoroutineSocketRelay(self::UNIX_SOCKET, null, CoroutineSocketRelay::SOCK_UNIX)
            );
            $this->baseExample($task);
        });
    }

    public function testConcurrently()
    {
        $container = new Container(new DefinitionSource([], new ScanConfig()));
        $container->set(ConfigInterface::class, new Config([
            'gotask' => [
                'enable' => true,
                'socket_address' => self::UNIX_SOCKET,
                'pool' => [
                    'min_connections' => 1,
                    'max_connections' => 100,
                    'connect_timeout' => 10.0,
                    'wait_timeout' => 3.0,
                    'heartbeat' => -1,
                    'max_idle_time' => (float) env('GOTASK_MAX_IDLE_TIME', 60),
                ],
            ],
        ]));
        $container->define(RPC::class, SocketIPCFactory::class);
        $container->define(
            StdoutLoggerInterface::class,
            StdoutLogger::class
        );
        ApplicationContext::setContainer($container);

        \Swoole\Coroutine\run(function () {
            sleep(1);
            $task = make(SocketGoTask::class);
            $wg = new WaitGroup();
            $wg->add();
            $this->baseExample($task);
            go(function () use ($wg, $task) {
                $this->baseExample($task);
                $wg->done();
            });
            $wg->add();
            go(function () use ($wg, $task) {
                $this->baseExample($task);
                $wg->done();
            });
            $wg->wait();
        });
    }

    public function baseExample($task)
    {
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
