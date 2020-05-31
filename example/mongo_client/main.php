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

use Reasno\GoTask\GoTask;
use Reasno\GoTask\IPC\SocketIPCSender;
use Swoole\Process;
use function Swoole\Coroutine\run;

require __DIR__ . '/../../vendor/autoload.php';

const ADDR = '127.0.0.1:6001';

exec('go build -o ' . __DIR__ . '/app ' . __DIR__ . '/sidecar.go');
$process = new Process(function (Process $process) {
    $process->exec(__DIR__ . '/app', ['-address', ADDR]);
});
$process->start();

sleep(1);

run(function () {
    $task = new SocketIPCSender(ADDR);
    for ($i = 0; $i < 5; $i++){
        $task->call('MongoProxy.InsertOne', ['Database' => 'testing', 'Collection' => 'colors', 'Record' => ['Blue' => 'Red', 'number' => $i]]);
    }
    var_dump($task->call('MongoProxy.Find', ['Database' => 'testing', 'Collection' => 'colors', 'Filter' => ['Blue' => 'Red'], 'Opts' => [['Skip' => 1, 'Limit' => 2]]]));
});
