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
use Hyperf\Process\AbstractProcess;
use Psr\Container\ContainerInterface;
use Reasno\GoTask\Exception\GoBuildException;
use Swoole\Server;

class GoTaskProcess extends AbstractProcess
{
    /**
     * @var string
     */
    public $name = 'gotask';

    public $enableCoroutine = true;

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->config = $container->get(ConfigInterface::class);
        if ($this->config->get('gotask.go_log.redirect', true)){
            $this->redirectStdinStdout = true;
        }
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
        if ($this->config->get('gotask.go2php.enable', false)){
            $argArr[] = '-go2php-address';
            $argArr[] = $this->config->get('gotask.go2php.address', '127.0.0.1:6002');
        }
        array_push($argArr, ...$args);
        $this->process->exec($executable, $argArr);
    }
}
