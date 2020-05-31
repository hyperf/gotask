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
use Hyperf\GoTask\IPC\SocketIPCReceiver;
use Swoole\Process;
use function Swoole\Coroutine\run;

require __DIR__ . '/../../vendor/autoload.php';

const ADDR = '127.0.0.1:6002';

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

class Example
{
    public function HelloString(string $payload)
    {
        return "Hello, {$payload}!";
    }

    public function HelloInterface(array $payload)
    {
        return ['hello' => $payload];
    }

    public function HelloStruct(array $payload)
    {
        return ['hello' => $payload];
    }

    public function HelloBytes(string $payload)
    {
        return new \Hyperf\GoTask\Wrapper\ByteWrapper(base64_encode($payload));
    }

    public function HelloError(array $payload)
    {
        throw new \Exception('err');
    }
}
