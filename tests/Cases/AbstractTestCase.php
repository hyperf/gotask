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
namespace HyperfTest\Cases;

use Hyperf\Config\Config;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Container;
use Hyperf\Di\Definition\DefinitionSource;
use Hyperf\Di\Definition\ScanConfig;
use Hyperf\Framework\Logger\StdoutLogger;
use Hyperf\GoTask\GoTask;
use Hyperf\GoTask\SocketGoTask;
use Hyperf\Utils\ApplicationContext;
use PHPUnit\Framework\TestCase;

/**
 * Class AbstractTestCase.
 */
abstract class AbstractTestCase extends TestCase
{
    const UNIX_SOCKET = __DIR__ . '/test.sock';

    public function setUp(): void
    {
        ! defined('BASE_PATH') && define('BASE_PATH', dirname(__DIR__, 1));
        @unlink(self::UNIX_SOCKET);
        $container = new Container(new DefinitionSource([], new ScanConfig()));
        $container->set(ConfigInterface::class, new Config([
            'gotask' => [
                'enable' => true,
                'socket_address' => self::UNIX_SOCKET,
                'pool' => [
                    'min_connections' => 1,
                    'max_connections' => 100,
                    'connect_timeout' => 10.0,
                    'wait_timeout' => 3.0,
                    'heartbeat' => -1,
                    'max_idle_time' => (float) env('GOTASK_MAX_IDLE_TIME', 60),
                ],
            ],
        ]));
        $container->define(
            StdoutLoggerInterface::class,
            StdoutLogger::class
        );
        $container->define(
            GoTask::class,
            SocketGoTask::class
        );
        ApplicationContext::setContainer($container);
    }
}
