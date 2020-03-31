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

return [
    'enable' => true,
    'executable' => BASE_PATH . '/bin/app',
    'socket_address' => \Reasno\GoTask\ConfigProvider::address(),
    'go2php' => [
        'enable' => false,
        'address' => \Reasno\GoTask\ConfigProvider::address(),
    ],
    'go_build' => [
        'enable' => false,
        'command' => sprintf(
            'go build -o %1$s/bin/app %1$s/gotask/cmd/app.go',
            BASE_PATH
        ),
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
