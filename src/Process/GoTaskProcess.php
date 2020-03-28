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

class GoTaskProcess extends AbstractProcess
{
    /**
     * @var string
     */
    public $name = 'gotask';

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->config = $container->get(ConfigInterface::class);
        $this->redirectStdinStdout = empty($this->config->get('gotask.socket_address')) ?? false;
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
        $args = $this->config->get('gotask.args', []);
        $argArr = ['-address',  $address];
        array_push($argArr, ...$args);
        $this->process->exec($executable, $argArr);
    }
}
