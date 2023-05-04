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
namespace Hyperf\GoTask\IPC;

interface IPCSenderInterface
{
    /** Payload flags.*/
    public const PAYLOAD_NONE = 2;

    public const PAYLOAD_RAW = 4;

    public const PAYLOAD_ERROR = 8;

    public const PAYLOAD_CONTROL = 16;

    /**
     * @param mixed $payload an binary data or array of arguments for complex types
     * @param int $flags payload control flags
     *
     * @return mixed
     */
    public function call(string $method, $payload, int $flags = 0);
}
