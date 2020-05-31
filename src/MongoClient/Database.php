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

class Database
{
    use MongoTrait;

    /**
     * @var string
     */
    protected $database;

    /**
     * @var MongoProxy
     */
    private $mongo;

    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(MongoProxy $mongo, ConfigInterface $config, string $database)
    {
        $this->mongo = $mongo;
        $this->config = $config;
        $this->database = $database;
    }

    public function __get($collName)
    {
        return new Collection($this->mongo, $this->config, $this->database, $collName);
    }

    public function collection($collName)
    {
        return new Collection($this->mongo, $this->config, $this->database, $collName);
    }

    public function runCommand($command = [], $opts = []): ?array
    {
        $payload = [
            'Database' => $this->database,
            'Command' => $this->sanitize($command),
            'Opts' => $this->sanitizeOpts($opts),
        ];
        return $this->mongo->runCommand($payload);
    }

    public function runCommandCursor($command = [], $opts = []): ?array
    {
        $payload = [
            'Database' => $this->database,
            'Command' => $this->sanitize($command),
            'Opts' => $this->sanitizeOpts($opts),
        ];
        return $this->mongo->runCommandCursor($payload);
    }
}
