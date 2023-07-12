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

use Hyperf\Command\Event\BeforeHandle;
use Hyperf\Event\Contract\ListenerInterface;
use Hyperf\Framework\Event\AfterWorkerStart;
use Hyperf\GoTask\Config\DomainConfig;
use Hyperf\GoTask\IPC\SocketIPCReceiver;
use Hyperf\GoTask\WithGoTask;

use function Hyperf\Coroutine\go;
use function Hyperf\Support\make;

class Go2PhpListener implements ListenerInterface
{
    public function __construct(
        private DomainConfig $config
    ) {
    }

    public function listen(): array
    {
        return [AfterWorkerStart::class, BeforeHandle::class];
    }

    public function process(object $event): void
    {
        if (! $this->config->shouldEnableGo2Php()) {
            return;
        }

        if ($event instanceof BeforeHandle && ! ($event->getCommand() instanceof WithGoTask)) {
            return;
        }

        $addr = $this->config->getGo2PhpAddress();
        if ($this->isUnix($addr)) {
            $addrArr = explode(',', $addr);
            if (count($addrArr) <= $event->workerId) {
                return;
            }
            $addr = $addrArr[$event->workerId];
        }

        go(function () use ($addr) {
            $server = make(SocketIPCReceiver::class, [$addr]);
            $server->start();
        });
    }

    private function isUnix(string $addr): bool
    {
        return strpos($addr, ':') === false;
    }
}
