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
namespace Hyperf\GoTask\Listener;

use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\GoTask\Config\DomainConfig;
use Hyperf\GoTask\WithGoTask;
use Swoole\Process;
use Symfony\Component\Console\Event\ConsoleCommandEvent;

class CommandListener implements ListenerInterface
{
    /**
     * @var Process
     */
    private $process;

    /**
     * @var DomainConfig
     */
    private $config;

    public function __construct(DomainConfig $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function listen(): array
    {
        return [
            ConsoleCommandEvent::class,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function process(object $event): void
    {
        if (! $this->config->isEnabled()) {
            return;
        }
        if (($event instanceof ConsoleCommandEvent) && ($event->getCommand() instanceof WithGoTask)) {
            $this->process = new Process(function (Process $process) {
                $executable = $this->config->getExecutable();
                $args = $this->config->getArgs();
                $process->exec($executable, $args);
            });
            $this->process->start();
            sleep(1);
        }
    }
}
