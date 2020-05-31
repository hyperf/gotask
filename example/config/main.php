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
use Hyperf\Config\Config;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Container;
use Hyperf\Di\Definition\DefinitionSource;
use Hyperf\Di\Definition\ScanConfig;
use Hyperf\Framework\Logger\StdoutLogger;
use Hyperf\GoTask\IPC\SocketIPCReceiver;
use Hyperf\Utils\ApplicationContext;
use Swoole\Process;
use function Swoole\Coroutine\run;

require __DIR__ . '/../../vendor/autoload.php';
define('BASE_PATH', __DIR__);

const ADDR = '127.0.0.1:6002';
$container = new Container(new DefinitionSource([], new ScanConfig()));
$container->set(ConfigInterface::class, new Config([
    'gotask' => [
        'enable' => true,
        'socket_address' => ADDR,
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
$container->define(
    StdoutLoggerInterface::class,
    StdoutLogger::class
);
ApplicationContext::setContainer($container);
exec('go build -o ' . __DIR__ . '/app ' . __DIR__ . '/sidecar.go');
$process = new Process(function (Process $process) {
    sleep(1);
    $process->exec(__DIR__ . '/app', ['-go2php-address', ADDR]);
}, false, 0, true);
$process->start();

run(function () {
    $server = new SocketIPCReceiver(ADDR);
    $server->start();
});
