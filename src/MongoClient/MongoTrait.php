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

namespace Hyperf\GoTask\MongoClient;

use stdClass;

trait MongoTrait
{
    private function sanitize(mixed $input): mixed
    {
        return $input ?: new stdClass();
    }

    private function sanitizeOpts(mixed $opts): mixed
    {
        return $this->sanitize($opts);
    }
}
