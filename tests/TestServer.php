<?php


namespace HyperfTest;

use Hyperf\Config\Config;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Container;
use Hyperf\Di\Definition\DefinitionSource;
use Hyperf\Di\Definition\ScanConfig;
use Hyperf\Framework\Logger\StdoutLogger;
use Hyperf\Utils\ApplicationContext;
use Reasno\GoTask\IPC\SocketIPCReceiver;
use Swoole\Process;
use Swoole\Timer;
use function Swoole\Coroutine\run;

require __DIR__ . '/../vendor/autoload.php';
define('BASE_PATH', __DIR__);

const ADDR = '/tmp/test.sock';
@unlink(ADDR);
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

run(function () {
    $server = new SocketIPCReceiver(ADDR);
    Timer::after(5000, function() use ($server){
       $server->close();
    });
    $server->start();
});
