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

namespace Reasno\GoTask\Listener;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\AfterWorkerStart;
use Reasno\GoTask\IPC\SocketIPCReceiver;

class OnWorkerStartListener implements ListenerInterface
{
    /**
     * @var SocketIPCReceiver
     */
    private $receiver;

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function listen(): array
    {
        return [AfterWorkerStart::class];
    }

    /**
     * {@inheritdoc}
     */
    public function process(object $event)
    {
        if ($this->config->get('gotask.enable_go2php', true)) {
            $addr = $this->config->get('gotask.go2php_addr', '/tmp/gotask_go2php.sock');
            $server = make(SocketIPCReceiver::class, $addr);
            $server->start();
        }
    }
}
