<?php


namespace Reasno\GoTask;

use Reasno\GoTask\GoTask;

class GoTaskProxy implements GoTask
{
    /**
     * @var GoTask
     */
    private $goTask;

    public function __construct(GoTask $goTask)
    {
        $this->goTask = $goTask;
    }

    public function __call($name, $arguments)
    {
        $method = ucfirst($name);
        return $this->call(__CLASS__.'.'.$method, ...$arguments);
    }

    /**
     * @inheritDoc
     */
    public function call(string $method, $payload, int $flags = 0)
    {
        $this->goTask->call($method, $payload, $flags);
    }
}
