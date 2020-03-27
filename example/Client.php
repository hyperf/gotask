<?php

declare(strict_types=1);
/**
 * This file is part of Reasno/RemoteGoTask.
 *
 * @link     https://www.github.com/reasno/gotask
 * @document  https://www.github.com/reasno/gotask
 * @contact  guxi99@gmail.com
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

use Reasno\GoTask\Relay\CoroutineSocketRelay;
use Spiral\Goridge\RelayInterface;
use Spiral\Goridge\RPC;
use function Swoole\Coroutine\run;

require '../vendor/autoload.php';

run(function () {
    $task = new RPC(
        new CoroutineSocketRelay('127.0.0.1', 6001)
    );
    var_dump($task->call('App.HelloString', 'Reasno'));
    var_dump($task->call('App.HelloInterface', ['jack', 'jill']));
    var_dump($task->call('App.HelloStruct', [
        'firstName' => 'LeBron',
        'lastName' => 'James',
        'id' => 23,
    ]));
    var_dump($task->call('App.HelloBytes', base64_encode('My Bytes'), RelayInterface::PAYLOAD_RAW));
    try {
        $task->call('App.HelloError', 'Reasno');
    } catch (\Throwable $e) {
        var_dump($e);
    }
});
