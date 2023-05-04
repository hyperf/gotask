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
    private function sanitize($input)
    {
        return $input ?: new stdClass();
    }

    private function sanitizeOpts($opts)
    {
        return $this->sanitize($opts);
    }
}
