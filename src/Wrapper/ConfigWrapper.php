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

namespace Reasno\GoTask\Wrapper;

use Hyperf\Contract\ConfigInterface;

class ConfigWrapper
{
    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    public function get($payload)
    {
        return $this->config->get($payload, null);
    }

    public function has($payload)
    {
        return $this->config->has($payload);
    }

    public function set($payload)
    {
        $this->config->set($payload['key'], $payload['value']);
        return null;
    }
}
