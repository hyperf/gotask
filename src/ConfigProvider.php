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
namespace Hyperf\GoTask;

use Hyperf\GoTask\Listener\CommandListener;
use Hyperf\GoTask\Listener\Go2PhpListener;
use Hyperf\GoTask\Listener\LogRedirectListener;
use Hyperf\GoTask\Listener\PipeLockListener;
use Hyperf\GoTask\Process\GoTaskProcess;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                GoTask::class => GoTaskFactory::class,
            ],
            'commands' => [
            ],
            'processes' => [
                GoTaskProcess::class,
            ],
            'listeners' => [
                CommandListener::class,
                PipeLockListener::class,
                LogRedirectListener::class,
                Go2PhpListener::class,
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                    'ignore_annotations' => [
                        'mixin',
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

    public static function address()
    {
        if (defined("BASE_PATH")) {
            $root = BASE_PATH . '/runtime';
        } else {
            $root = '/tmp';
        }

        $appName = env('APP_NAME');
        $socketName = $appName . '_' . uniqid();
        return $root . "/{$socketName}.sock";
    }
}
