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

use function Hyperf\Support\make;

class SocketIPCFactory
{
    public function __construct(
        private DomainConfig $config
    ) {
    }

    public function make(): SocketIPCSender
    {
        $address = $this->config->getAddress();
        return make(SocketIPCSender::class, ['address' => $address]);
    }
}
