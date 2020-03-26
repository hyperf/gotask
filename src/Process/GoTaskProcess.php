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
use Swoole\Atomic;

class GoTaskProcess extends AbstractProcess
{
    /**
     * @var Atomic
     */
    public static $taskPid;

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->config = $container->get(ConfigInterface::class);
        self::$taskPid = new Atomic();
    }

    public function isEnable(): bool
    {
        return $this->config->get('gotask.enable', false);
    }

    /**
     * {@inheritdoc}
     */
    public function handle(): void
    {
        $executable = $this->config->get('gotask.executable', BASE_PATH . '/app');
        $address = $this->config->get('gotask.socket_address', '/tmp/gotask.sock');
        self::$taskPid->set($this->process->pid);
        $this->process->exec($executable, ['-address',  $address]);
    }
}
