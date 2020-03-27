<?php


namespace Reasno\GoTask;


interface GoTask
{
    public function call(string $method, $payload, int $flags = 0);
}
