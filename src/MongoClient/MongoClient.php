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

    /**
     * @var array
     */
    private $typeMap;

    public function __construct(MongoProxy $mongo, ConfigInterface $config)
    {
        $this->mongo = $mongo;
        $this->config = $config;
        $this->typeMap = $this->config->get('mongodb.type_map', ['document' => 'array', 'root' => 'array']);
    }

    public function __get($dbName)
    {
        return new Database($this->mongo, $this->config, $dbName, $this->typeMap);
    }

    public function database(string $dbName)
    {
        return new Database($this->mongo, $this->config, $dbName, $this->typeMap);
    }
}
