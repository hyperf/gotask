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
use function MongoDB\BSON\fromPHP;
use function MongoDB\BSON\toPHP;

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

    public function runCommand($command = [], $opts = [], $typeMap = ['document' => 'array',  'root' => 'array'])
    {
        $payload = [
            'Database' => $this->database,
            'Command' => $this->sanitize($command),
            'Opts' => $this->sanitizeOpts($opts),
        ];
        return toPHP($this->mongo->runCommand(fromPHP($payload)), $typeMap);
    }

    public function runCommandCursor($command = [], $opts = [], $typeMap = ['document' => 'array',  'root' => 'array'])
    {
        $payload = [
            'Database' => $this->database,
            'Command' => $this->sanitize($command),
            'Opts' => $this->sanitizeOpts($opts),
        ];
        return toPHP($this->mongo->runCommandCursor(fromPHP($payload)), $typeMap);
    }
}
