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

namespace Reasno\GoTask\Process;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\ExceptionHandler\Formatter\FormatterInterface;
use Hyperf\Process\AbstractProcess;
use Hyperf\Process\Exception\SocketAcceptException;
use Hyperf\Process\ProcessCollector;
use Psr\Container\ContainerInterface;
use Reasno\GoTask\Exception\GoBuildException;
use Swoole\Coroutine;
use Swoole\Process;
use Swoole\Server;

class GoTaskProcess extends AbstractProcess
{
    /**
     * @var string
     */
    public $name = 'gotask';

    public $redirectStdinStdout = true;

    public $enableCoroutine = true;

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->config = $container->get(ConfigInterface::class);
    }

    public function isEnable(): bool
    {
        return $this->config->get('gotask.enable', false);
    }

    public function bind(Server $server): void
    {
        if ($this->config->get('gotask.go_build.enable', false)) {
            exec($this->config->get('gotask.go_build.command'), $output, $rev);
            if ($rev !== 0) {
                throw new GoBuildException(sprintf(
                    'Cannot build go files with command %s: %s',
                    $this->config->get('gotask.go_build.command'),
                    $output
                ));
            }
        }
        parent::bind($server);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(): void
    {
        $executable = $this->config->get('gotask.executable', BASE_PATH . '/app');
        $address = $this->config->get('gotask.socket_address', '/tmp/gotask.sock');
        $args = $this->config->get('gotask.args', []);
        $argArr = ['-address', $address];
        array_push($argArr, ...$args);
        $this->process->exec($executable, $argArr);
    }
}
