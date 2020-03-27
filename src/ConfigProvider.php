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

namespace Reasno\GoTask;

use Reasno\GoTask\Process\GoTaskProcess;
use Spiral\Goridge\RPC;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                GoTask::class => RemoteGoTask::class,
            ],
            'commands' => [
            ],
            'processes' => [
                GoTaskProcess::class,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for gotask.',
                    'source' => __DIR__ . '/../publish/gotask.php',
                    'destination' => BASE_PATH . '/config/autoload/gotask.php',
                ],
                [
                    'id' => 'app',
                    'description' => 'The go main package template for gotask.',
                    'source' => __DIR__ . '/../cmd/app.go',
                    'destination' => BASE_PATH . '/gotask/cmd/app.go',
                ],
                [
                    'id' => 'gomod',
                    'description' => 'The go.mod for gotask.',
                    'source' => __DIR__ . '/../publish/go.mod',
                    'destination' => BASE_PATH . '/gotask/go.mod',
                ],
            ],
        ];
    }
}
