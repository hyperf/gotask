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

use Hyperf\Contract\ConfigInterface;

class MongoClient
{
    use MongoTrait;

    /**
     * @var MongoProxy
     */
    private $mongo;

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(MongoProxy $mongo, ConfigInterface $config)
    {
        $this->mongo = $mongo;
        $this->config = $config;
    }

    public function __get($dbName)
    {
        return new Database($this->mongo, $this->config, $dbName);
    }

    public function database(string $dbName)
    {
        return new Database($this->mongo, $this->config, $dbName);
    }
}
