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
        $path = explode('\\', static::class);
        $class = array_pop($path);
        return $this->call($class . '.' . $method, ...$arguments);
    }

    /**
     * {@inheritdoc}
     */
    public function call(string $method, $payload, int $flags = 0)
    {
        return $this->goTask->call($method, $payload, $flags);
    }
}
