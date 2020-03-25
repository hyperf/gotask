<?php


namespace Reasno\GoTask\Process;


use Hyperf\Contract\ConfigInterface;
use Hyperf\Process\AbstractProcess;
use Psr\Container\ContainerInterface;
use Swoole\Atomic;

class GoTaskProcess extends AbstractProcess
{
    /**
     * @var ConfigInterface
     */
    private $config;
    /**
     * @var Atomic
     */
    public static $taskPid;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->config = $container->get(ConfigInterface::class);
        self::$taskPid = new Atomic();
    }

    public function isEnable(): bool
    {
        return $this->config->get('gotask.enable', false);
    }

    /**
     * @inheritDoc
     */
    public function handle(): void
    {
        $executable = $this->config->get('gotask.executable', BASE_PATH."/app");
        $address = $this->config->get('gotask.socket_address', "/tmp/gotask.sock");
        self::$taskPid->set($this->process->pid);
        $this->process->exec($executable, ["-address",  $address]);
    }
}
