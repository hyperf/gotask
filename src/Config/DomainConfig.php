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
namespace Hyperf\GoTask\Config;

use Hyperf\Contract\ConfigInterface;

class DomainConfig
{
    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function getProcessName(): string
    {
        return 'gotask';
    }

    public function getExecutable(): string
    {
        return $this->config->get('gotask.executable', BASE_PATH . '/app');
    }

    public function isEnabled(): bool
    {
        return $this->config->get('gotask.enable', false) || $this->config->get('gotask.enabled', false);
    }

    public function getAddress(): string
    {
        return $this->config->get('gotask.socket_address', '127.0.0.1:6001');
    }

    public function getArgs(): array
    {
        $args = $this->config->get('gotask.args', []);
        $argArr = ['-address', $this->getAddress()];
        if ($this->shouldEnableGo2Php()) {
            $argArr[] = '-go2php-address';
            $argArr[] = $this->getGo2PhpAddress();
        }
        return array_merge($argArr, $args);
    }

    public function shouldBuild(): bool
    {
        return $this->config->get('gotask.go_build.enable', false);
    }

    public function getBuildWorkdir(): string
    {
        return $this->config->get('gotask.go_build.workdir', BASE_PATH . '/gotask');
    }

    public function getBuildCommand(): string
    {
        return $this->config->get('gotask.go_build.command');
    }

    public function shouldLogRedirect(): bool
    {
        return $this->config->get('gotask.go_log.redirect', true);
    }

    public function getLogLevel(): string
    {
        return $this->config->get('gotask.go_log.level', 'info');
    }

    public function shouldEnableGo2Php(): bool
    {
        return $this->config->get('gotask.go2php.enable', false);
    }

    public function getGo2PhpAddress(): string
    {
        return $this->config->get('gotask.go2php.address', '127.0.0.1:6002');
    }

    public function getPoolOptions(): array
    {
        return $this->config->get('gotask.pool', []);
    }
}
