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

namespace Reasno\GoTask;

interface GoTask
{
    /** Payload flags.*/
    const PAYLOAD_NONE = 2;

    const PAYLOAD_RAW = 4;

    const PAYLOAD_ERROR = 8;

    const PAYLOAD_CONTROL = 16;

    /**
     * @param mixed $payload an binary data or array of arguments for complex types
     * @param int $flags payload control flags
     *
     * @return mixed
     */
    public function call(string $method, $payload, int $flags = 0);
}
