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

namespace Hyperf\GoTask\Wrapper;

use Psr\Log\LoggerInterface;

class LoggerWrapper
{
    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public function log(array $payload): mixed
    {
        $this->logger->log($payload['level'], $payload['message'], $payload['context']);
        return null;
    }
}
