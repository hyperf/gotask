<?php

use Reasno\GoTask\Relay\CoroutineSocketRelay;
use Spiral\Goridge\RPC;
use function Swoole\Coroutine\run;

require_once "../vendor/autoload.php";

run(function(){
    $task = new RPC(
        new CoroutineSocketRelay("127.0.0.1", 6001)
    );
    var_dump($task->call("App.Hi", "Antony"));
});
