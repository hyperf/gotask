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
namespace Hyperf\GoTask\Process;

use Hyperf\GoTask\Config\DomainConfig;
use Hyperf\GoTask\Exception\GoBuildException;
use Hyperf\Process\AbstractProcess;
use Psr\Container\ContainerInterface;

class GoTaskProcess extends AbstractProcess
{
    public bool $enableCoroutine = true;

    /**
     * @var DomainConfig
     */
    private $config;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->config = $container->get(DomainConfig::class);
        $this->redirectStdinStdout = $this->config->shouldLogRedirect();
        $this->name = $this->config->getProcessName();
    }

    public function isEnable($server): bool
    {
        return $this->config->isEnabled();
    }

    public function bind($server): void
    {
        if ($this->config->shouldBuild()) {
            chdir($this->config->getBuildWorkdir());
            exec($this->config->getBuildCommand(), $output, $rev);
            if ($rev !== 0) {
                throw new GoBuildException(sprintf(
                    'Cannot build go files with command %s: %s',
                    $this->config->getBuildCommand(),
                    implode(PHP_EOL, $output)
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
        $executable = $this->config->getExecutable();
        $args = $this->config->getArgs();
        $this->process->exec($executable, $args);
    }
}
