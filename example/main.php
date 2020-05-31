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
use Hyperf\GoTask\GoTask;
use Hyperf\GoTask\IPC\SocketIPCSender;
use Swoole\Process;
use function Swoole\Coroutine\run;

require __DIR__ . '/../vendor/autoload.php';

const ADDR = '127.0.0.1:6001';

exec('go build -o ' . __DIR__ . '/app ' . __DIR__ . '/sidecar.go');
$process = new Process(function (Process $process) {
    $process->exec(__DIR__ . '/app', ['-address', ADDR]);
});
$process->start();

sleep(1);

run(function () {
    $task = new SocketIPCSender(ADDR);
    var_dump($task->call('App.HelloString', 'Hyperf'));
    var_dump($task->call('App.HelloInterface', ['jack', 'jill']));
    var_dump($task->call('App.HelloStruct', [
        'firstName' => 'LeBron',
        'lastName' => 'James',
        'id' => 23,
    ]));
    var_dump($task->call('App.HelloBytes', base64_encode('My Bytes'), GoTask::PAYLOAD_RAW));
    try {
        $task->call('App.HelloError', 'Hyperf');
    } catch (\Throwable $e) {
        var_dump($e);
    }
    try {
        $task->call('App.HelloPanic', '');
    } catch (\Throwable $e) {
        var_dump($e);
    }
});
