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
namespace Hyperf\GoTask;

use Hyperf\GoTask\Config\DomainConfig;
use Hyperf\GoTask\IPC\SocketIPCSender;

class SocketIPCFactory
{
    /**
     * @var DomainConfig
     */
    private $config;

    public function __construct(DomainConfig $config)
    {
        $this->config = $config;
    }

    public function make()
    {
        $address = $this->config->getAddress();
        return make(SocketIPCSender::class, ['address' => $address]);
    }
}
