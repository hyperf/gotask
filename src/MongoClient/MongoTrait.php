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

namespace Reasno\GoTask\MongoClient;

trait MongoTrait
{
    private function ucFirstKeys(array $arr)
    {
        foreach ($arr as $key => $value) {
            unset($arr[$key]);
            $arr[ucfirst($key)] = $value;
        }
        return $arr;
    }

    private function sanitize($input)
    {
        return $input ?: new \stdClass();
    }

    private function sanitizeOpts($opts)
    {
        return [$this->sanitize($this->ucFirstKeys($opts))];
    }
}
