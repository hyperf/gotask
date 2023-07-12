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
    public function __construct(
        private GoTask $goTask
    ) {
    }

    public function __call(string $name, array $arguments): mixed
    {
        $method = ucfirst($name);
        $path = explode('\\', static::class);
        $class = array_pop($path);
        return $this->call($class . '.' . $method, ...$arguments);
    }

    public function call(string $method, mixed $payload, int $flags = 0): mixed
    {
        return $this->goTask->call($method, $payload, $flags);
    }
}
