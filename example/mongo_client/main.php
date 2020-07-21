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
use Hyperf\GoTask\IPC\SocketIPCSender;
use Hyperf\GoTask\MongoClient\MongoClient;
use Hyperf\GoTask\MongoClient\MongoProxy;
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
    $client = new MongoClient(new MongoProxy($task), new Config([]));
    $collection = $client->database('testing')->collection('unit');
    $collection->insertOne(['foo' => 'bar', 'tid' => 0]);
});
