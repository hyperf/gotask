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
namespace HyperfTest;

class Stub
{
    public function echo($payload)
    {
        return $payload;
    }

    public function HelloString(string $payload)
    {
        return "Hello, {$payload}!";
    }

    public function HelloInterface(array $payload)
    {
        return ['hello' => $payload];
    }

    public function HelloStruct(array $payload)
    {
        return ['hello' => $payload];
    }

    public function HelloBytes(string $payload)
    {
        return base64_decode($payload);
    }

    public function HelloError(array $payload)
    {
        throw new \Exception();
    }
}
