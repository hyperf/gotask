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
return [
    'enable' => true,
    'executable' => BASE_PATH . '/bin/app',
    'socket_address' => \Hyperf\GoTask\ConfigProvider::address(),
    'go2php' => [
        'enable' => false,
        'address' => \Hyperf\GoTask\ConfigProvider::address(),
    ],
    'go_build' => [
        'enable' => false,
        'workdir' => BASE_PATH . '/gotask',
        'command' => 'go build -o ../bin/app cmd/app.go',
    ],
    'go_log' => [
        'redirect' => true,
        'level' => 'info',
    ],
    'pool' => [
        'min_connections' => 1,
        'max_connections' => 30,
        'connect_timeout' => 10.0,
        'wait_timeout' => 30.0,
        'heartbeat' => -1,
        'max_idle_time' => (float) env('GOTASK_MAX_IDLE_TIME', 60),
    ],
];
