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

use Reasno\GoTask\Relay\CoroutineSocketRelay;
use Spiral\Goridge\RPC;
use function Swoole\Coroutine\run;

require_once '../vendor/autoload.php';

run(function () {
    $task = new RPC(
        new CoroutineSocketRelay('127.0.0.1', 6001)
    );
    var_dump($task->call('App.Hi', 'Antony'));
});
