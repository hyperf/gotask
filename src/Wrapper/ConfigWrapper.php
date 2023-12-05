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

namespace Hyperf\GoTask\Wrapper;

use Hyperf\Contract\ConfigInterface;

class ConfigWrapper
{
    public function __construct(
        private ConfigInterface $config
    ) {
    }

    public function get(string $payload)
    {
        return $this->config->get($payload, null);
    }

    public function has(string $payload): bool
    {
        return $this->config->has($payload);
    }

    public function set(string $payload): ?mixed
    {
        $this->config->set($payload['key'], $payload['value']);
        return null;
    }
}
