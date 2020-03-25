<?php


namespace Reasno\GoTask;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ConnectionInterface;
use Hyperf\Pool\Frequency;
use Hyperf\Pool\Pool;
use Hyperf\Utils\Arr;
use Psr\Container\ContainerInterface;

class GoTaskConnectionPool extends Pool
{
    public function __construct(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class);
        $key = 'gotask';
        if (! $config->has($key)) {
            throw new \InvalidArgumentException(sprintf('config[%s] is not exist!', $key));
        }

        $this->config = $config->get($key);
        $options = Arr::get($this->config, 'pool', []);

        $this->frequency = make(Frequency::class);

        parent::__construct($container, $options);
    }

    public function createConnection(): ConnectionInterface
    {
        return make(GoTaskConnection::class, ['pool' => $this]);
    }
}
